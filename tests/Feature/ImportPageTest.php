<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use function Pest\Livewire\livewire;
use SWalbrun\FilamentModelImport\Filament\Pages\ImportPage;
use SWalbrun\FilamentModelImport\Import\ModelMapping\AssociationOf;
use SWalbrun\FilamentModelImport\Import\ModelMapping\AssociationRegister;
use SWalbrun\FilamentModelImport\Import\ModelMapping\IdentificationOf;
use SWalbrun\FilamentModelImport\Import\ModelMapping\IdentificationRegister;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\IdentificationOfBlog;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\IdentificationOfPost;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\IdentificationOfRole;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\IdentificationOfUser;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\Blog;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\Post;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\User;

it('can create an user and roles by import', function () {
    $fileToImport = getDefaultXlsx('UserImport.xlsx');
    $identificationOfUser = new IdentificationOfUser();
    registerIdentification($identificationOfUser);
    registerIdentification(new IdentificationOfRole());
    registerAssociationOf($identificationOfUser);
    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => [uuid_create() => $fileToImport],
        ]);

    /** @var User $importedUser */
    $importedUser = User::query()->where(User::COL_NAME, '=', 'Sebastian')->first();
    expect($importedUser)->not->toBeNull()
        ->and($importedUser->roles()->where('name', '=', 'admin')->count())->toBe(1)
        ->and($importedUser->roles()->where('name', '=', 'bidderRoundParticipant')->count())->toBe(1);
});

it('can update an user by import', function () {
    User::query()->create([
        'name' => 'Sebastian12',
        'password' => Hash::make('password!'),
        'email' => 'ws-1993@gmx.de',
    ]);

    $identificationOfUser = new IdentificationOfUser();
    registerIdentification($identificationOfUser);
    registerIdentification(new IdentificationOfRole());
    registerAssociationOf($identificationOfUser);

    $fileToImport = getDefaultXlsx('UserImport.xlsx');
    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => [uniqid() => $fileToImport],
        ])->assertSuccessful();

    /** @var User $importedUser */
    $importedUser = User::query()->where(User::COL_NAME, '=', 'Sebastian')->first();
    expect($importedUser->name)
        ->toBe('Sebastian')
        ->and(User::query()->count())->toBe(1);
});

it('does not call the relation hook if the method argument types do not match', function () {
    $blog = mockBlog();
    $post = mockPost();

    IdentificationOfPost::$hasHookBeenCalled = false;

    registerIdentification(new IdentificationOfBlog($blog));
    registerIdentification(new IdentificationOfPost($post));
    registerAssociationOf(fn (stdClass $post, IdentificationOfBlog $blog) => IdentificationOfPost::$hasHookBeenCalled = true);

    $fileToImport = getDefaultXlsx('PropertyImport.xlsx');
    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => [uniqid() => $fileToImport],
        ]);
    expect(IdentificationOfPost::$hasHookBeenCalled)->toBeFalsy();
});

it('does call the relation hook if the method argument types match', function () {
    $blog = mockBlog();
    $post = mockPost();
    IdentificationOfPost::$hasHookBeenCalled = false;

    registerIdentification(new IdentificationOfBlog($blog));
    registerIdentification(new IdentificationOfPost($post));
    registerAssociationOf(fn (Post $post, Blog $blog) => IdentificationOfPost::$hasHookBeenCalled = true);

    $fileToImport = getDefaultXlsx('PropertyImport.xlsx');
    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => [uniqid() => $fileToImport],
        ]);
    expect(IdentificationOfPost::$hasHookBeenCalled)->toBeTruthy();
});

it('throws an exception for', function (IdentificationOf $modelMapping) {
    registerIdentification(new IdentificationOfUser());
    registerIdentification(new IdentificationOfRole());
    registerIdentification($modelMapping);

    $fileToImport = getDefaultXlsx('UserImport.xlsx');
    expect(fn () => livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => [uniqid() => $fileToImport],
        ])->send())->toThrow(Exception::class, "The regex's result is overlapping");
})->with([
    'regex matching between two models' => fn () => new class extends IdentificationOf
    {
        public function __construct()
        {
            parent::__construct(new User());
        }

        public function propertyMapping(): Collection
        {
            return collect([
                'matchAll' => '/.*/i',
            ]);
        }

        public function uniqueColumns(): array
        {
            return [];
        }
    },
    'regex matching within same model' => fn () => new class extends IdentificationOf
    {
        public function __construct()
        {
            parent::__construct(new User());
        }

        public function propertyMapping(): Collection
        {
            return collect([
                'productNumber' => '/Product Number/i',
                'userNumber' => '/Number/i',
            ]);
        }

        public function uniqueColumns(): array
        {
            return [];
        }
    },

]);

function getDefaultXlsx(string $fileName): UploadedFile
{
    return new UploadedFile(
        __DIR__.'/../__data__/Files/'.$fileName,
        $fileName,
        null,
        null,
        true
    );
}

function mockPost(): Post
{
    $postMock = Mockery::mock(Post::class)->makePartial();
    $postMock->shouldReceive('save')->andReturn(true);
    $postMock->shouldReceive('newInstance')->andReturn($postMock);
    $postMock->shouldReceive('getAttributes')->passthru();
    $postMock->fillable(['property']);
    $postBuilderMock = Mockery::mock(Builder::class);
    $postBuilderMock->shouldReceive('firstOrNew')->andReturn($postMock);
    $postMock->shouldReceive('newQuery')->andReturn($postBuilderMock);

    return $postMock;
}

function mockBlog(): Blog
{
    $blogMock = Mockery::mock(Blog::class)->makePartial();
    $blogMock->shouldReceive('save')->andReturn(true);
    $blogMock->shouldReceive('newInstance')->andReturn($blogMock);
    $blogMock->shouldReceive('getAttributes')->passthru();
    $blogMock->shouldReceive('fill');
    $blogBuilderMock = Mockery::mock(Builder::class);
    $blogBuilderMock->shouldReceive('firstOrNew')->andReturn($blogMock);
    $blogBuilderMock->shouldReceive('updateOrCreate')->andReturn($blogMock);
    $blogMock->shouldReceive('newQuery')
        ->andReturn(
            $blogBuilderMock
        );

    return $blogMock;
}

function registerIdentification(IdentificationOf $identificationOfUser)
{
    /** @var IdentificationRegister $identificationRegister */
    $identificationRegister = resolve(IdentificationRegister::class);
    $identificationRegister
        ->register($identificationOfUser);
}

function registerAssociationOf(AssociationOf|Closure $associationOf)
{
    /** @var AssociationRegister $associationRegister */
    $associationRegister = resolve(AssociationRegister::class);
    if ($associationOf instanceof AssociationOf) {
        $associationRegister->registerAssociationOf($associationOf);
    } else {
        $associationRegister->registerClosure(
            $associationOf
        );
    }
}
