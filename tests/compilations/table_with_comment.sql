CREATE TABLE some_table (
  col_one String COMMENT 'Column\'s comment'
)
ENGINE = MergeTree()
ORDER BY (col_one)
SETTINGS ttl_only_drop_parts = 1
COMMENT 'Table\'s comment'
