<?php

namespace Paymenter\Extensions\Others\DomainName\Registers;

use App\Models\User;

interface IRegister
{
    /**
     * Get the register configuration
     *
     * @return array
     */
    public function getConfig(): array;

    /**
     * Check if the necessary configuration values are set and not null.
     *
     * @return bool
     */
    public static function isConfigurable(): bool;

    /**
     * Search for a domain name
     * @param string $domainName
     * @return array<array{
     *         domain: string,
     *         price: string,
     *         available: bool
     *     }>
     * }
     */
    public function searchDomain(string $domainName): array;

    /**
     * Register a domain name with user information
     *
     * @param string $domainName
     * @param User $user
     * @param integer $years
     * @return boolean
     */
    public function registerDomain(string $domainName, User $user, int $years = 1): bool;

    /**
     * Renew a domain registration for a specific number of years
     *
     * @param string $domainName
     * @param integer $years
     * @return boolean
     */
    public function renewDomain(string $domainName, int $years = 1): bool;

    /**
     * Update domain information (like contact details)
     *
     * @param string $domainName
     * @param User $user
     * @return boolean
     */
    public function updateDomainInfo(string $domainName,  User $user): bool;

    /**
     * Change the nameservers for a domain
     *
     * @param string $domainName
     * @param array $nameservers
     * @return boolean
     */
    public function changeNameservers(string $domainName, array $nameservers): bool;

    /**
     * Undocumented function
     *
     * @param string $domainName
     * @return array{
     *         domain: string,
     *         price: string,
     *         available: bool
     *     }
     */
    public function getDomain(string $domainName): array;
}
