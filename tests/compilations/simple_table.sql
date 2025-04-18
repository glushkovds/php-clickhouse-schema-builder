CREATE TABLE some_table (
  col_one String
)
ENGINE = MergeTree()
ORDER BY (col_one)
