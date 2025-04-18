CREATE TABLE some_table (
  col_one String,
  at DateTime
)
ENGINE = MergeTree()
ORDER BY (col_one)
TTL at + INTERVAL 1 month
