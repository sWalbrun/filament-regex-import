<?php

use Filament\Navigation\NavigationItem;
use SWalbrun\FilamentModelImport\Filament\Pages\ImportPage;

it('takes care of the label translation', function () {
    $label = trans('filament-regex-import::filament-regex-import.resource.navigation.label');
    /** @var NavigationItem $navigation */
    $navigation = ImportPage::getNavigationItems()[0];

    expect($navigation->getLabel())->toBe($label);
});

it('takes care of the navigation group', function () {
    $group = 'TEST';
    config(['filament-regex-import.navigation_group' => $group]);
    /** @var NavigationItem $navigation */
    $navigation = ImportPage::getNavigationItems()[0];

    expect($navigation->getGroup())->toBe($group);
});
