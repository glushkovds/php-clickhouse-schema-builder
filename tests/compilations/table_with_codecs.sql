CREATE TABLE some_table (
  at DateTime64(3) COMMENT 'some comment' CODEC(DoubleDelta, ZSTD(1)),
  col_one String DEFAULT '' CODEC(ZSTD(3))
)
ENGINE = MergeTree()
ORDER BY (at)
