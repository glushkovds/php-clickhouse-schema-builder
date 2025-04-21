CREATE TABLE some_table (
  col_one String
)
ENGINE = MergeTree()
ORDER BY (col_one)
SETTINGS ttl_only_drop_parts = 1, index_granularity = 8192
