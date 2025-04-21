CREATE TABLE some_table (
  col_one String
)
ENGINE = ReplicatedReplacingMergeTree('/clickhouse/tables/some_db.some_table', '{replica}', col_one)
ORDER BY (col_one)
