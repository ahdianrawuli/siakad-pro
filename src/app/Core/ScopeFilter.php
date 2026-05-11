<?php
namespace App\Core;

/**
 * ScopeFilter — helper untuk menerapkan filter jenjang (MTS/MA/PDF/GLOBAL)
 * ke query yang melibatkan tabel classrooms.
 *
 * Penggunaan:
 *   [$scopeWhere, $scopeParams] = ScopeFilter::apply('c');
 *   $where .= $scopeWhere;
 *   $params = array_merge($params, $scopeParams);
 */
class ScopeFilter {
    /**
     * @param string $classAlias  alias tabel classrooms di query (default 'c')
     * @return array [string $whereClause, array $params]
     */
    public static function apply(string $classAlias = 'c'): array {
        $scope = Session::get('active_scope', 'GLOBAL') ?? 'GLOBAL';
        if ($scope && $scope !== 'GLOBAL') {
            return [" AND {$classAlias}.major = ?", [$scope]];
        }
        return ['', []];
    }

    public static function get(): string {
        return Session::get('active_scope', 'GLOBAL') ?? 'GLOBAL';
    }

    public static function isActive(): bool {
        $s = self::get();
        return $s && $s !== 'GLOBAL';
    }
}
