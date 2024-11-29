<?php

namespace Paymenter\Extensions\Others\DomainName\Registers;

use Exception;
use Paymenter\Extensions\Others\DomainName\DomainName;

final class Helper
{

    /** @var IRegister|null */
    private static ?IRegister $staticRegister = null;

    /**
     * Used to read all Registers in extensions/Others/DomainName/Register
     *
     * @return array
     */
    public static function getRegisters()
    {
        // Read app/Extensions directory
        $basePath = base_path('extensions/Others/DomainName/Registers');
        $availableRegisters = array_filter(scandir($basePath), function ($item) use ($basePath) {
            return is_dir($basePath . DIRECTORY_SEPARATOR . $item) && $item !== '.' && $item !== '..';
        });

        foreach ($availableRegisters as $key => $register) {
            $registers[] = $register;
        }

        return $registers;
    }

    /**
     * Get Register and return new instance
     *
     * @param  string  $nameRegister
     * @return IRegister
     */
    public static function getRegister(): IRegister
    {
        if (self::$staticRegister !== null) {
            return self::$staticRegister;
        }

        $nameRegister = DomainName::getConfigValue('register_name');
        $register = '\\Paymenter\\Extensions\\Others\\DomainName\\Registers\\' . $nameRegister . '\\' . $nameRegister;
        if (!class_exists($register)) {
            throw new Exception('Register not found');
        }

        self::$staticRegister = new $register;

        return self::$staticRegister;
    }
}
