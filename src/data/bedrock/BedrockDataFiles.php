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

namespace pocketmine\data\bedrock;

use const pocketmine\BEDROCK_DATA_PATH;

final class BedrockDataFiles{
	private function __construct(){
		//NOOP
	}

	public const BANNER_PATTERNS_JSON = BEDROCK_DATA_PATH . '/banner_patterns.json';
	public const BIOME_DEFINITIONS_NBT = BEDROCK_DATA_PATH . '/biome_definitions.nbt';
	public const BIOME_DEFINITIONS_FULL_NBT = BEDROCK_DATA_PATH . '/biome_definitions_full.nbt';
	public const BIOME_ID_MAP_JSON = BEDROCK_DATA_PATH . '/biome_id_map.json';
	public const BLOCK_ID_TO_ITEM_ID_MAP_JSON = BEDROCK_DATA_PATH . '/block_id_to_item_id_map.json';
	public const BLOCK_STATE_META_MAP_1_19_10_JSON = BEDROCK_DATA_PATH . '/block_state_meta_map-1.19.10.json';
	public const BLOCK_STATE_META_MAP_1_19_40_JSON = BEDROCK_DATA_PATH . '/block_state_meta_map-1.19.40.json';
	public const BLOCK_STATE_META_MAP_1_19_50_JSON = BEDROCK_DATA_PATH . '/block_state_meta_map-1.19.50.json';
	public const BLOCK_STATE_META_MAP_1_19_63_JSON = BEDROCK_DATA_PATH . '/block_state_meta_map-1.19.63.json';
	public const BLOCK_STATE_META_MAP_1_19_70_JSON = BEDROCK_DATA_PATH . '/block_state_meta_map-1.19.70.json';
	public const BLOCK_STATE_META_MAP_JSON = BEDROCK_DATA_PATH . '/block_state_meta_map.json';
	public const CANONICAL_BLOCK_STATES_1_18_10_NBT = BEDROCK_DATA_PATH . '/canonical_block_states-1.18.10.nbt';
	public const CANONICAL_BLOCK_STATES_1_18_30_NBT = BEDROCK_DATA_PATH . '/canonical_block_states-1.18.30.nbt';
	public const CANONICAL_BLOCK_STATES_1_19_10_NBT = BEDROCK_DATA_PATH . '/canonical_block_states-1.19.10.nbt';
	public const CANONICAL_BLOCK_STATES_1_19_40_NBT = BEDROCK_DATA_PATH . '/canonical_block_states-1.19.40.nbt';
	public const CANONICAL_BLOCK_STATES_1_19_50_NBT = BEDROCK_DATA_PATH . '/canonical_block_states-1.19.50.nbt';
	public const CANONICAL_BLOCK_STATES_1_19_63_NBT = BEDROCK_DATA_PATH . '/canonical_block_states-1.19.63.nbt';
	public const CANONICAL_BLOCK_STATES_1_19_70_NBT = BEDROCK_DATA_PATH . '/canonical_block_states-1.19.70.nbt';
	public const CANONICAL_BLOCK_STATES_NBT = BEDROCK_DATA_PATH . '/canonical_block_states.nbt';
	public const COMMAND_ARG_TYPES_JSON = BEDROCK_DATA_PATH . '/command_arg_types.json';
	public const CREATIVEITEMS_JSON = BEDROCK_DATA_PATH . '/creativeitems.json';
	public const ENTITY_ID_MAP_JSON = BEDROCK_DATA_PATH . '/entity_id_map.json';
	public const ENTITY_IDENTIFIERS_NBT = BEDROCK_DATA_PATH . '/entity_identifiers.nbt';
	public const ITEM_TAGS_JSON = BEDROCK_DATA_PATH . '/item_tags.json';
	public const LEVEL_SOUND_ID_MAP_JSON = BEDROCK_DATA_PATH . '/level_sound_id_map.json';
	public const PARTICLE_ID_MAP_JSON = BEDROCK_DATA_PATH . '/particle_id_map.json';
	public const R12_TO_CURRENT_BLOCK_MAP_1_18_10_BIN = BEDROCK_DATA_PATH . '/r12_to_current_block_map-1.18.10.bin';
	public const R12_TO_CURRENT_BLOCK_MAP_1_18_30_BIN = BEDROCK_DATA_PATH . '/r12_to_current_block_map-1.18.30.bin';
	public const R12_TO_CURRENT_BLOCK_MAP_1_19_63_BIN = BEDROCK_DATA_PATH . '/r12_to_current_block_map-1.19.63.bin';
	public const R12_TO_CURRENT_BLOCK_MAP_1_19_70_BIN = BEDROCK_DATA_PATH . '/r12_to_current_block_map-1.19.70.bin';
	public const R12_TO_CURRENT_BLOCK_MAP_BIN = BEDROCK_DATA_PATH . '/r12_to_current_block_map.bin';
	public const R16_TO_CURRENT_ITEM_MAP_JSON = BEDROCK_DATA_PATH . '/r16_to_current_item_map.json';
	public const REQUIRED_ITEM_LIST_1_18_10_JSON = BEDROCK_DATA_PATH . '/required_item_list-1.18.10.json';
	public const REQUIRED_ITEM_LIST_1_18_30_JSON = BEDROCK_DATA_PATH . '/required_item_list-1.18.30.json';
	public const REQUIRED_ITEM_LIST_1_19_0_JSON = BEDROCK_DATA_PATH . '/required_item_list-1.19.0.json';
	public const REQUIRED_ITEM_LIST_1_19_40_JSON = BEDROCK_DATA_PATH . '/required_item_list-1.19.40.json';
	public const REQUIRED_ITEM_LIST_1_19_50_JSON = BEDROCK_DATA_PATH . '/required_item_list-1.19.50.json';
	public const REQUIRED_ITEM_LIST_1_19_63_JSON = BEDROCK_DATA_PATH . '/required_item_list-1.19.63.json';
	public const REQUIRED_ITEM_LIST_1_19_70_JSON = BEDROCK_DATA_PATH . '/required_item_list-1.19.70.json';
	public const REQUIRED_ITEM_LIST_JSON = BEDROCK_DATA_PATH . '/required_item_list.json';
}
