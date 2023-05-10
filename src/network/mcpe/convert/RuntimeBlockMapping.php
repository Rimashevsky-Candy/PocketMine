<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\network\mcpe\convert;

use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\data\bedrock\BedrockDataFiles;
use pocketmine\data\bedrock\LegacyBlockIdToStringIdMap;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\player\Player;
use pocketmine\utils\BinaryStream;
use pocketmine\utils\Filesystem;
use pocketmine\utils\SingletonTrait;
use function str_replace;

/**
 * @internal
 */
final class RuntimeBlockMapping{
	use SingletonTrait;

	public const CANONICAL_BLOCK_STATES_PATH = 0;
	public const R12_TO_CURRENT_BLOCK_MAP_PATH = 1;

	/** @var int[][] */
	private array $legacyToRuntimeMap = [];
	/** @var int[][] */
	private array $runtimeToLegacyMap = [];
	/** @var CompoundTag[][] */
	private array $bedrockKnownStates = [];

	private static function make() : self{
		$protocolPaths = [
			ProtocolInfo::CURRENT_PROTOCOL => [
				self::CANONICAL_BLOCK_STATES_PATH => '',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '',
			],
			ProtocolInfo::PROTOCOL_1_19_70 => [
				self::CANONICAL_BLOCK_STATES_PATH => '-1.19.70',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '-1.19.70',
			],
			ProtocolInfo::PROTOCOL_1_19_63 => [
				self::CANONICAL_BLOCK_STATES_PATH => '-1.19.63',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '-1.19.63',
			],
			ProtocolInfo::PROTOCOL_1_19_50 => [
				self::CANONICAL_BLOCK_STATES_PATH => '-1.19.50',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '-1.19.63',
			],
			ProtocolInfo::PROTOCOL_1_19_40 => [
				self::CANONICAL_BLOCK_STATES_PATH => '-1.19.40',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '-1.19.63',
			],
			ProtocolInfo::PROTOCOL_1_19_10 => [
				self::CANONICAL_BLOCK_STATES_PATH => '-1.19.10',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '-1.19.63',
			],
			ProtocolInfo::PROTOCOL_1_18_30 => [
				self::CANONICAL_BLOCK_STATES_PATH => '-1.18.30',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '-1.18.30',
			],
			ProtocolInfo::PROTOCOL_1_18_10 => [
				self::CANONICAL_BLOCK_STATES_PATH => '-1.18.10',
				self::R12_TO_CURRENT_BLOCK_MAP_PATH => '-1.18.10',
			]
		];

		$canonicalBlockStatesFiles = [];
		$r12ToCurrentBlockMapFiles = [];

		foreach($protocolPaths as $protocol => $paths){
			$canonicalBlockStatesFiles[$protocol] = str_replace(".nbt", $paths[self::CANONICAL_BLOCK_STATES_PATH] . ".nbt", BedrockDataFiles::CANONICAL_BLOCK_STATES_NBT);
			$r12ToCurrentBlockMapFiles[$protocol] = str_replace(".bin", $paths[self::R12_TO_CURRENT_BLOCK_MAP_PATH] . ".bin", BedrockDataFiles::R12_TO_CURRENT_BLOCK_MAP_BIN);
		}

		return new self(
			$canonicalBlockStatesFiles,
			$r12ToCurrentBlockMapFiles
		);
	}

	/**
	 * @param string[] $keyIndex
	 * @param (ByteTag|StringTag|IntTag)[][] $valueIndex
	 * @phpstan-param array<string, string> $keyIndex
	 * @phpstan-param array<int, array<int|string, ByteTag|IntTag|StringTag>> $valueIndex
	 */
	private static function deduplicateCompound(CompoundTag $tag, array &$keyIndex, array &$valueIndex) : CompoundTag{
		if($tag->count() === 0){
			return $tag;
		}

		$newTag = CompoundTag::create();
		foreach($tag as $key => $value){
			$key = $keyIndex[$key] ??= $key;

			if($value instanceof CompoundTag){
				$value = self::deduplicateCompound($value, $keyIndex, $valueIndex);
			}elseif($value instanceof ByteTag || $value instanceof IntTag || $value instanceof StringTag){
				$value = $valueIndex[$value->getType()][$value->getValue()] ??= $value;
			}

			$newTag->setTag($key, $value);
		}

		return $newTag;
	}

	/**
	 * @param string[] $canonicalBlockStatesFiles
	 * @param string[] $r12ToCurrentBlockMapFiles
	 */
	private function __construct(array $canonicalBlockStatesFiles, array $r12ToCurrentBlockMapFiles){
		foreach($canonicalBlockStatesFiles as $mappingProtocol => $canonicalBlockStatesFile){
			$stream = new BinaryStream(Filesystem::fileGetContents($canonicalBlockStatesFile));
			$list = [];
			$nbtReader = new NetworkNbtSerializer();

			$keyIndex = [];
			$valueIndex = [];
			while(!$stream->feof()){
				$offset = $stream->getOffset();
				$blockState = $nbtReader->read($stream->getBuffer(), $offset)->mustGetCompoundTag();
				$stream->setOffset($offset);
				$list[] = self::deduplicateCompound($blockState, $keyIndex, $valueIndex);
			}
			$this->bedrockKnownStates[$mappingProtocol] = $list;
		}

		foreach($r12ToCurrentBlockMapFiles as $mappingProtocol => $r12ToCurrentBlockMapFile){
			$this->setupLegacyMappings($mappingProtocol, $r12ToCurrentBlockMapFile);
		}
	}

	public static function getMappingProtocol(int $protocolId) : int{
		if($protocolId === ProtocolInfo::PROTOCOL_1_19_60){
			return ProtocolInfo::PROTOCOL_1_19_63;
		}

		if($protocolId < ProtocolInfo::PROTOCOL_1_19_40 && $protocolId >= ProtocolInfo::PROTOCOL_1_19_0){
			if($protocolId === ProtocolInfo::PROTOCOL_1_19_0){
				return ProtocolInfo::PROTOCOL_1_19_10;
			}

			if($protocolId >= ProtocolInfo::PROTOCOL_1_19_20){
				return ProtocolInfo::PROTOCOL_1_19_40;
			}
		}

		return $protocolId;
	}

	/**
	 * @param Player[] $players
	 *
	 * @return Player[][]
	 */
	public static function sortByProtocol(array $players) : array{
		$sortPlayers = [];

		foreach($players as $player){
			$mappingProtocol = self::getMappingProtocol($player->getNetworkSession()->getProtocolId());

			if(isset($sortPlayers[$mappingProtocol])){
				$sortPlayers[$mappingProtocol][] = $player;
			}else{
				$sortPlayers[$mappingProtocol] = [$player];
			}
		}

		return $sortPlayers;
	}

	private function setupLegacyMappings(int $mappingProtocol, string $r12ToCurrentBlockMapFile) : void{
		$legacyIdMap = LegacyBlockIdToStringIdMap::getInstance();
		/** @var R12ToCurrentBlockMapEntry[] $legacyStateMap */
		$legacyStateMap = [];
		$legacyStateMapReader = new BinaryStream(Filesystem::fileGetContents($r12ToCurrentBlockMapFile));
		$nbtReader = new NetworkNbtSerializer();
		while(!$legacyStateMapReader->feof()){
			$id = $legacyStateMapReader->get($legacyStateMapReader->getUnsignedVarInt());
			$meta = $legacyStateMapReader->getLShort();

			$offset = $legacyStateMapReader->getOffset();
			$state = $nbtReader->read($legacyStateMapReader->getBuffer(), $offset)->mustGetCompoundTag();
			$legacyStateMapReader->setOffset($offset);
			$legacyStateMap[] = new R12ToCurrentBlockMapEntry($id, $meta, $state);
		}

		/**
		 * @var int[][] $idToStatesMap string id -> int[] list of candidate state indices
		 */
		$idToStatesMap = [];
		foreach($this->bedrockKnownStates[$mappingProtocol] as $k => $state){
			$idToStatesMap[$state->getString("name")][] = $k;
		}
		foreach($legacyStateMap as $pair){
			$id = $legacyIdMap->stringToLegacy($pair->getId());
			if($id === null){
				throw new \RuntimeException("No legacy ID matches " . $pair->getId());
			}
			$data = $pair->getMeta();
			if($data > 15){
				//we can't handle metadata with more than 4 bits
				continue;
			}
			$mappedState = $pair->getBlockState();
			$mappedName = $mappedState->getString("name");
			if(!isset($idToStatesMap[$mappedName])){
				throw new \RuntimeException("Mapped new state does not appear in network table");
			}
			foreach($idToStatesMap[$mappedName] as $k){
				$networkState = $this->bedrockKnownStates[$mappingProtocol][$k];
				if($mappedState->equals($networkState)){
					$this->registerMapping($mappingProtocol, $k, $id, $data);
					continue 2;
				}
			}
			throw new \RuntimeException("Mapped new state does not appear in network table");
		}
	}

	public function toRuntimeId(int $internalStateId, int $mappingProtocol = ProtocolInfo::CURRENT_PROTOCOL) : int{
		return $this->legacyToRuntimeMap[$internalStateId][$mappingProtocol] ?? $this->legacyToRuntimeMap[BlockLegacyIds::INFO_UPDATE << Block::INTERNAL_METADATA_BITS][$mappingProtocol];
	}

	public function fromRuntimeId(int $runtimeId, int $mappingProtocol = ProtocolInfo::CURRENT_PROTOCOL) : int{
		return $this->runtimeToLegacyMap[$runtimeId][$mappingProtocol];
	}

	private function registerMapping(int $mappingProtocol, int $staticRuntimeId, int $legacyId, int $legacyMeta) : void{
		$this->legacyToRuntimeMap[($legacyId << Block::INTERNAL_METADATA_BITS) | $legacyMeta][$mappingProtocol] = $staticRuntimeId;
		$this->runtimeToLegacyMap[$staticRuntimeId][$mappingProtocol] = ($legacyId << Block::INTERNAL_METADATA_BITS) | $legacyMeta;
	}

	/**
	 * @return CompoundTag[]
	 */
	public function getBedrockKnownStates(int $mappingProtocol = ProtocolInfo::CURRENT_PROTOCOL) : array{
		return $this->bedrockKnownStates[$mappingProtocol];
	}
}
