<?php
namespace App\Extensoes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UTCDateTimeType extends DateTimeType
{
    /**
     * @var \DateTimeZone
     */
    private static $utc;

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (null === $value || $value instanceof \DateTime) {
            return $value;
        }

        $converted = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            self::getUtc()
        );

        // Verifica se é um datetime(Y-m-d H:i:s)
        if ($converted !== false) {
            $converted = \DateTime::createFromFormat(
                $platform->getTimeFormatString(),
                $value,
                self::getUtc()
            );
            // Verifica se é um time(H:i)
            if ($converted !== false) {
                throw ConversionException::conversionFailedFormat(
                    $value,
                    $this->getName(),
                    $platform->getDateTimeFormatString()
                );
            }
        }

        return $converted;
    }

    private static function getUtc(): \DateTimeZone
    {
        if (is_null(self::$utc) === true) {
            self::$utc = new \DateTimeZone('UTC');
        }

        return self::$utc;
    }


}
