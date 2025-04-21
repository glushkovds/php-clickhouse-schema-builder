CREATE TABLE some_table ON CLUSTER 'some_cluster' (
  col_one String
)
ENGINE = MergeTree()
ORDER BY (col_one)
