<?php

declare(strict_types=1);

namespace Diw\Pannellum\Updates;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

/**
 * Migrates existing panorama content elements from the deprecated "General Plugin"
 * (CType "list" + list_type "pannellum_panorama") to the dedicated content type
 * (CType) "pannellum_panorama".
 */
#[UpgradeWizard('pannellumListTypeToCTypeUpdate')]
final class PannellumListTypeToCTypeUpdate extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'pannellum_panorama' => 'pannellum_panorama',
        ];
    }

    public function getTitle(): string
    {
        return 'Migrate Pannellum "360° Panorama" plugins to a dedicated content type (CType)';
    }

    public function getDescription(): string
    {
        return 'The Pannellum panorama plugin is now registered as its own content type. '
            . 'This wizard converts existing content elements from the deprecated '
            . '"General Plugin" (CType "list", list_type "pannellum_panorama") to the new '
            . 'CType "pannellum_panorama". FlexForm settings and the preview image are preserved.';
    }
}
