CREATE TABLE some_table (
  col_one String
)
ENGINE = ReplacingMergeTree(col_one)
ORDER BY (col_one)
