CREATE TABLE some_table (
  col_one String,
  col_two String,
  INDEX col_two_idx col_two TYPE bloom_filter GRANULARITY 4,
  INDEX col_one_len_idx length(col_one) TYPE minmax
)
ENGINE = MergeTree()
ORDER BY (col_one)
