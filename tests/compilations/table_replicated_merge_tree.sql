CREATE TABLE some_table (
  col_one String
)
ENGINE = ReplicatedMergeTree('/clickhouse/tables/some_db.some_table', '{replica}')
ORDER BY (col_one)
