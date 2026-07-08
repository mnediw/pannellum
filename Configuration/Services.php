<?php

declare(strict_types=1);

use Diw\Pannellum\Controller\PannellumController;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Information\Typo3Version;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services()
        ->defaults()
            ->autowire()
            ->autoconfigure()
            ->private();

    $services->load('Diw\\Pannellum\\', '../Classes/*')
        ->exclude('../Classes/Updates/*');

    $services->set(PannellumController::class)
        ->public()
        ->tag('extbase.controller');

    // The list_type -> CType upgrade wizard extends AbstractListTypeToCTypeUpdate,
    // which only exists in TYPO3 v13. Registering it (and therefore reflecting the
    // class) on v12.4 would fatal, so load it only on v13+. The extension itself
    // works on both v12.4 and v13.
    if ((new Typo3Version())->getMajorVersion() >= 13) {
        $services->load('Diw\\Pannellum\\Updates\\', '../Classes/Updates/*');
    }
};
