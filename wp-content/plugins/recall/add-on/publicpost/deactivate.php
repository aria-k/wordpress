<?phpglobal $rcl_options;unset($rcl_options['info_author_recall']);$rcl_options = serialize($rcl_options);update_option('primary-rcl-options',$rcl_options);?>