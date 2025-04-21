CREATE TABLE some_table (
  col_one String,
  at DateTime
)
ENGINE = MergeTree()
PARTITION BY toDate(at)
ORDER BY (col_one)
