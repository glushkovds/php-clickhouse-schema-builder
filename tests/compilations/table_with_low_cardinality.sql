CREATE TABLE some_table (
  col_one LowCardinality(String) COMMENT 'some comment',
  col_two LowCardinality(Nullable(String)),
  col_three String
)
ENGINE = MergeTree()
ORDER BY (col_one)
