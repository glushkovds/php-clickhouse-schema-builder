CREATE TABLE some_table (
  col_one String DEFAULT '5' COMMENT 'some comment',
  col_two String DEFAULT col_one
)
ENGINE = MergeTree()
ORDER BY (col_one)
