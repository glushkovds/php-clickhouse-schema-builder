<?php

namespace PhpClickHouseSchemaBuilder;

class Syntax
{
    const RESERVED_WORDS = ['add', 'after', 'algorithm', 'alias', 'all', 'alter', 'and', 'anti', 'any', 'append',
        'apply', 'as', 'asc', 'ascending', 'asof', 'assume', 'ast', 'async', 'attach', 'azure', 'backup',
        'bagexpansion', 'between', 'bidirectional', 'both', 'by', 'cascade', 'case', 'cast', 'change',
        'changed', 'char', 'character', 'check', 'cleanup', 'cluster', 'clusters', 'cn', 'codec', 'collate',
        'column', 'columns', 'comment', 'commit', 'compression', 'const', 'constraint', 'create', 'cross', 'cube',
        'currentuser', 'd', 'data', 'database', 'databases', 'date', 'day', 'days', 'dd', 'deduplicate', 'default',
        'definer', 'delete', 'desc', 'descending', 'describe', 'detach', 'dictionaries', 'dictionary', 'disk',
        'distinct', 'div', 'drop', 'else', 'empty', 'end', 'enforced', 'engine', 'ephemeral', 'estimate', 'event',
        'events', 'every', 'except', 'exists', 'explain', 'expression', 'extended', 'false', 'fetch', 'fields',
        'file', 'filter', 'final', 'first', 'following', 'for', 'foreign', 'format', 'freeze', 'from', 'full',
        'fulltext', 'function', 'global', 'grant', 'grantees', 'granularity', 'groups', 'h', 'hash', 'having',
        'hdfs', 'hh', 'hierarchical', 'host', 'hour', 'hours', 'http', 'id', 'identified', 'ilike', 'in',
        'index', 'indexes', 'indices', 'inherit', 'injective', 'inner', 'interpolate', 'intersect',
        'interval', 'invisible', 'invoker', 'ip', 'join', 'jwt', 'kerberos', 'key', 'keys', 'kill', 'kind', 'last',
        'layout', 'ldap', 'leading', 'left', 'level', 'lifetime', 'lightweight', 'like', 'limit', 'linear', 'list',
        'live', 'local', 'm', 'match', 'materialize', 'materialized', 'max', 'mcs', 'memory', 'merges', 'metrics',
        'mi', 'microsecond', 'microseconds', 'millisecond', 'milliseconds', 'min', 'minute', 'minutes', 'mm',
        'mod', 'modify', 'month', 'months', 'move', 'ms', 'mutation', 'n', 'name', 'nanosecond', 'nanoseconds',
        'next', 'none', 'not', 'ns', 'null', 'nulls', 'offset', 'on', 'only', 'or', 'outer', 'over', 'overridable',
        'part', 'partial', 'partition', 'partitions', 'paste', 'permanently', 'permissive', 'persistent', 'pipeline',
        'plan', 'populate', 'preceding', 'precision', 'prefix', 'prewhere', 'primary', 'profile', 'projection',
        'protobuf', 'pull', 'q', 'qq', 'quarter', 'quarters', 'query', 'quota', 'randomized', 'range', 'readonly',
        'realm', 'recompress', 'references', 'refresh', 'regexp', 'remove', 'rename', 'replace', 'restore', 'restrict',
        'restrictive', 'resume', 'revoke', 'right', 'rollback', 'rollup', 'row', 'rows', 's', 's3', 'salt', 'sample',
        'san', 'scheme', 'second', 'seconds', 'select', 'semi', 'server', 'set', 'settings', 'show', 'signed',
        'simple', 'skip', 'source', 'spatial', 'ss', 'statistics', 'step', 'storage', 'strict', 'subpartition',
        'subpartitions', 'suspend', 'sync', 'syntax', 'system', 'table', 'tables', 'tag', 'tags', 'temporary', 'test',
        'then', 'timestamp', 'to', 'top', 'totals', 'trailing', 'transaction', 'trigger', 'true', 'truncate', 'ttl',
        'type', 'typeof', 'unbounded', 'undrop', 'unfreeze', 'union', 'unique', 'unsigned', 'update', 'url', 'use',
        'using', 'uuid', 'values', 'varying', 'view', 'visible', 'watch', 'watermark', 'week', 'weeks', 'when',
        'where', 'window', 'qualify', 'with', 'recursive', 'wk', 'writable', 'ww', 'year', 'years', 'yy', 'yyyy',
        'zkpath', 'allowed_lateness', 'auto_increment', 'base_backup', 'bcrypt_hash', 'bcrypt_password',
        'changeable_in_readonly', 'cluster_host_ids', 'current_user', 'double_sha1_hash', 'double_sha1_password',
        'is_object_id', 'no_password', 'part_move_to_shard', 'plaintext_password', 'sha256_hash', 'sha256_password',
        'sql_tsi_day', 'sql_tsi_hour', 'sql_tsi_microsecond', 'sql_tsi_millisecond', 'sql_tsi_minute', 'sql_tsi_month',
        'sql_tsi_nanosecond', 'sql_tsi_quarter', 'sql_tsi_second', 'sql_tsi_week', 'sql_tsi_year', 'ssh_key',
        'ssl_certificate', 'strictly_ascending', 'with_itemindex'];

    public static function escapeName(string $elementName): string
    {
        $elementName = trim($elementName);
        if (!in_array($elementName, self::RESERVED_WORDS)) {
            return $elementName;
        }
        return '"' . $elementName . '"';
    }

    public static function escapeParam(mixed $value)
    {
        return match (true) {
            is_string($value) => "'$value'",
            is_bool($value) => $value ? 'true' : 'false',
            $value instanceof Expression => $value->value,
            default => $value,
        };
    }
}