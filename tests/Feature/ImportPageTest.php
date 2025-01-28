<?php

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use SWalbrun\FilamentModelImport\Filament\Pages\ImportPage;
use SWalbrun\FilamentModelImport\Import\ModelMapping\BaseMapper;
use SWalbrun\FilamentModelImport\Import\ModelMapping\MappingRegistrar;
use SWalbrun\FilamentModelImport\Import\ModelMapping\RelationRegistrar;
use SWalbrun\FilamentModelImport\Import\ModelMapping\Relator;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\BlogMapper;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\PostMapper;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\RoleMapper;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\UserMapper;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\Blog;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\Post;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\User;

use function Pest\Livewire\livewire;

it('can create an user and roles by import', function () {
    $fileToImport = getUserImportCsv();
    $userMapper = new UserMapper;
    registerMapper($userMapper);
    registerMapper(new RoleMapper);
    registerRelator($userMapper);

    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => $fileToImport,
        ])
        ->callAction('save')
        ->assertSuccessful();

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

    $userMapper = new UserMapper;
    registerMapper($userMapper);
    registerMapper(new RoleMapper);
    registerRelator($userMapper);

    $fileToImport = getUserImportCsv();
    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => $fileToImport,
        ])
        ->callAction('save')
        ->assertSuccessful();

    /** @var User $importedUser */
    $importedUser = User::query()->where(User::COL_NAME, '=', 'Sebastian')->first();
    expect($importedUser->name)
        ->toBe('Sebastian')
        ->and(User::query()->count())->toBe(1);
});

it('does not call the relation hook if the method argument types do not match', function () {
    $blog = mockBlog();
    $post = mockPost();

    PostMapper::$hasHookBeenCalled = false;

    registerMapper(new BlogMapper($blog));
    registerMapper(new PostMapper($post));
    registerRelator(fn (stdClass $post, BlogMapper $blog) => PostMapper::$hasHookBeenCalled = true);

    $fileToImport = getPropertyImportCsv();
    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => $fileToImport,
        ])
        ->callAction('save')
        ->assertSuccessful();
    expect(PostMapper::$hasHookBeenCalled)->toBeFalsy();
});

it('does call the relation hook if the method argument types match', function () {
    $blog = mockBlog();
    $post = mockPost();
    PostMapper::$hasHookBeenCalled = false;

    registerMapper(new BlogMapper($blog));
    registerMapper(new PostMapper($post));
    registerRelator(fn (Post $post, Blog $blog) => PostMapper::$hasHookBeenCalled = true);

    $fileToImport = getPropertyImportCsv();
    livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => $fileToImport,
        ])
        ->callAction('save')
        ->assertSuccessful();
    expect(PostMapper::$hasHookBeenCalled)->toBeTruthy();
});

it('throws an exception for', function (BaseMapper $modelMapping) {
    registerMapper(new UserMapper);
    registerMapper(new RoleMapper);
    registerMapper($modelMapping);

    $fileToImport = getUserImportCsv();
    expect(fn () => livewire(ImportPage::class)
        ->fillForm([
            ImportPage::IMPORT => $fileToImport,
        ])
        ->callAction('save'))
        ->toThrow(Exception::class, "The regex's result is overlapping");
})->with([
    'regex matching between two models' => fn () => new class extends BaseMapper
    {
        public function __construct()
        {
            parent::__construct(new User);
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
    'regex matching within same model' => fn () => new class extends BaseMapper
    {
        public function __construct()
        {
            parent::__construct(new User);
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

function getUserImportCsv(): UploadedFile
{
    $content = <<<'CSV'
"Benutzername","E-Mail","Beitrittsdatum","Beitragsgruppe","Anzahl d. Anteile","Angelegt am","Rolle","Slug Rolle","Product Number"
"Sebastian","ws-1993@gmx.de","44211","FULL_MEMBER","1","44197","admin","admin","Unit"
"Sebastian","ws-1993@gmx.de","44211","FULL_MEMBER","1","44197","bidderRoundParticipant","bidderRoundParticipant","Test"
CSV;

    return UploadedFile::fake()->createWithContent('UserImport.csv', $content);
}

function getPropertyImportCsv(): UploadedFile
{
    $content = <<<'CSV'
"PostName","BlogName"
"PropsGehenRaus","KenBlock"
CSV;

    return UploadedFile::fake()->createWithContent('PropertyImport.csv', $content);
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

function registerMapper(BaseMapper $mapper): void
{
    /** @var MappingRegistrar $identificationRegister */
    $identificationRegister = resolve(MappingRegistrar::class);
    $identificationRegister
        ->register($mapper);
}

function registerRelator(Relator|Closure $relator): void
{
    /** @var RelationRegistrar $associationRegister */
    $associationRegister = resolve(RelationRegistrar::class);
    if ($relator instanceof Relator) {
        $associationRegister->registerRelator($relator);
    } else {
        $associationRegister->registerClosure(
            $relator
        );
    }
}
