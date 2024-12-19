<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ApiGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:api {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create API files: model, migration, controller, store request, update request, resource.';

    public function handle()
    {
        $this->info('Creating magic... 🪄');

        $this->createModel();
        $this->createSeeder();
        $this->createController();
        $this->createRequests();
        $this->createResource();
        $this->modifyMigration();
        $this->modifyRepository();
        $this->createTest();
        $this->createFactory();

        $this->comment('Playground created successfully. Happy coding hugo! 🚀');
    }

    protected function createModel()
    {
        $name = $this->argument('name');
        $this->call('make:model', ['name' => $name, '-m' => true]);

        $modelPath = app_path("Models/{$name}.php");

        $modelContent = "<?php\n\n";
        $modelContent .= "namespace App\Models;\n\n";
        $modelContent .= "use Illuminate\Database\Eloquent\Factories\HasFactory;\n";
        $modelContent .= "use Illuminate\Database\Eloquent\Model;\n";
        $modelContent .= "use Illuminate\Database\Eloquent\SoftDeletes;\n";
        $modelContent .= "class {$name} extends Model\n";
        $modelContent .= "{\n";
        $modelContent .= "    use HasFactory, SoftDeletes;\n\n";
        $modelContent .= "    protected \$fillable = [\n";
        $modelContent .= "        // Add your columns here\n";
        $modelContent .= "    ];\n";
        $modelContent .= "}\n";

        file_put_contents($modelPath, $modelContent);
    }

    protected function createSeeder()
    {
        $name = $this->argument('name');
        $this->call('make:seeder', ['name' => "{$name}Seeder"]);
    }

    protected function createRequests()
    {
        $name = $this->argument('name');
        $this->call('make:request', ['name' => "Store{$name}Request"]);
        $this->call('make:request', ['name' => "Update{$name}Request"]);

        $storeRequestPath = app_path("Http/Requests/Store{$name}Request.php");

        $storeRequestContent =
            <<<'EOT'
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class Store__namePascalCase__Request extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        'code' => 'required|string|max:255|unique:__nameSnakeCasePlurals__,code',

                        'is_active' => 'nullable|boolean',
                    ];
                }

                public function prepareForValidation()
                {
                    if ($this->has('is_active')) {
                        $this->merge([
                            'is_active' => $this->is_active !== null ? filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN) : null,
                        ]);
                    }
                }
            }
            EOT;

        $storeRequestContent = str_replace('__namePascalCase__', $name, $storeRequestContent);
        $storeRequestContent = str_replace('__nameCamelCase__', Str::camel($name), $storeRequestContent);
        $storeRequestContent = str_replace('__nameSnakeCase__', Str::snake($name), $storeRequestContent);
        $storeRequestContent = str_replace('__nameSnakeCasePlurals__', Str::snake(Str::plural($name)), $storeRequestContent);
        $storeRequestContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $storeRequestContent);
        $storeRequestContent = str_replace('__nameKebabCase__', Str::kebab($name), $storeRequestContent);
        $storeRequestContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $storeRequestContent);
        $storeRequestContent = str_replace('__nameTitleCase__', ucfirst(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name)), $storeRequestContent);

        file_put_contents($storeRequestPath, $storeRequestContent);

        $updateRequestPath = app_path("Http/Requests/Update{$name}Request.php");

        $updateRequestContent =
            <<<'EOT'
            <?php

            namespace App\Http\Requests;

            use Illuminate\Foundation\Http\FormRequest;

            class Update__namePascalCase__Request extends FormRequest
            {
                /**
                 * Get the validation rules that apply to the request.
                 *
                 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
                 */
                public function rules()
                {
                    return [
                        'code' => 'required|string|max:255|unique:__nameSnakeCasePlurals__,code,'.$this->route('__nameSnakeCase__'),

                        'is_active' => 'required|boolean',
                    ];
                }

                public function prepareForValidation()
                {
                    if ($this->has('is_active')) {
                        $this->merge([
                            'is_active' => $this->is_active !== null ? filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN) : null,
                        ]);
                    }
                }
            }
            EOT;

        $updateRequestContent = str_replace('__namePascalCase__', $name, $updateRequestContent);
        $updateRequestContent = str_replace('__nameCamelCase__', Str::camel($name), $updateRequestContent);
        $updateRequestContent = str_replace('__nameSnakeCase__', Str::snake($name), $updateRequestContent);
        $updateRequestContent = str_replace('__nameSnakeCasePlurals__', Str::snake(Str::plural($name)), $updateRequestContent);
        $updateRequestContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $updateRequestContent);
        $updateRequestContent = str_replace('__nameKebabCase__', Str::kebab($name), $updateRequestContent);
        $updateRequestContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $updateRequestContent);
        $updateRequestContent = str_replace('__nameTitleCase__', ucfirst(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name)), $updateRequestContent);

        file_put_contents($updateRequestPath, $updateRequestContent);
    }

    protected function createController()
    {
        $name = $this->argument('name');
        $this->call('make:controller', ['name' => "Api/{$name}Controller", '--api' => true]);

        $controllerPath = app_path("Http/Controllers/Api/{$name}Controller.php");

        $controllerContent =
            <<<'EOT'
            <?php

            namespace App\Http\Controllers\Api;

            use App\Http\Controllers\Controller;
            use App\Http\Requests\Store__namePascalCase__Request;
            use App\Http\Requests\Update__namePascalCase__Request;
            use App\Http\Resources\__namePascalCase__Resource;
            use App\Interfaces\__namePascalCase__RepositoryInterface;
            use Illuminate\Http\Request;
            use App\Helpers\ResponseHelper;

            class __namePascalCase__Controller extends Controller
            {
                protected $__nameCamelCase__Repository;

                public function __construct(__namePascalCase__RepositoryInterface $__nameCamelCase__Repository)
                {
                    $this->__nameCamelCase__Repository = $__nameCamelCase__Repository;

                    $this->middleware('permission:__nameKebabCase__-list', ['only' => ['index', 'getAllActive', 'show', 'checkAvailability']]);
                    $this->middleware('permission:__nameKebabCase__-create', ['only' => ['store']]);
                    $this->middleware('permission:__nameKebabCase__-edit', ['only' => ['update', 'updateActiveStatus']]);
                    $this->middleware('permission:__nameKebabCase__-delete', ['only' => ['destroy']]);
                }

                public function index(Request $request)
                {
                    try {
                        $__nameCamelCasePlurals__ = $this->__nameCamelCase__Repository->getAll($request->all());

                        return ResponseHelper::jsonResponse(true, 'Success', __namePascalCase__Resource::collection($__nameCamelCasePlurals__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function show($id)
                {
                    try {
                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->getById($id, false);

                        if (! $__nameCamelCase__) {
                            return ResponseHelper::jsonResponse(false, '__nameTitleCase__ tidak tersedia.', null, 404);
                        }

                        return ResponseHelper::jsonResponse(true, 'Success', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function store(Store__namePascalCase__Request $request)
                {
                    $request = $request->validated();

                    if (!isset($request['is_active']) || $request['is_active'] === null) {
                        $request['is_active'] = true;
                    }

                    try {
                        $code = $request['code'];
                        if ($code == 'AUTO') {
                            $tryCount = 0;
                            do {
                                $code = $this->__nameCamelCase__Repository->generateCode($tryCount);
                                $tryCount++;
                            } while (! $this->__nameCamelCase__Repository->isUniqueCode($code));
                            $request['code'] = $code;
                        }

                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->create($request);

                        return ResponseHelper::jsonResponse(true, 'Data __nameTitleCase__ berhasil ditambahkan.', new __namePascalCase__Resource($__nameCamelCase__), 201);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function update(Update__namePascalCase__Request $request, $id)
                {
                    $request = $request->validated();

                    try {
                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->getById($id, false);
                        if (! $__nameCamelCase__) {
                            return ResponseHelper::jsonResponse(false, '__nameTitleCase__ tidak tersedia.', null, 404);
                        }

                        $code = $request['code'];
                        if ($code == 'AUTO') {
                            $tryCount = 0;
                            do {
                                $code = $this->__nameCamelCase__Repository->generateCode($tryCount);
                                $tryCount++;
                            } while (! $this->__nameCamelCase__Repository->isUniqueCode($code, $id));
                            $request['code'] = $code;
                        }

                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->update($request, $id);

                        return ResponseHelper::jsonResponse(true, 'Data __nameTitleCase__ berhasil diubah.', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }

                public function destroy($id)
                {
                    try {
                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->getById($id, false);
                        if (! $__nameCamelCase__) {
                            return ResponseHelper::jsonResponse(false, '__nameTitleCase__ tidak tersedia.', null, 404);
                        }

                        $__nameCamelCase__ = $this->__nameCamelCase__Repository->delete($id);

                        return ResponseHelper::jsonResponse(true, 'Data __nameTitleCase__ berhasil dihapus.', new __namePascalCase__Resource($__nameCamelCase__), 200);
                    } catch (\Exception $e) {
                        return ResponseHelper::jsonResponse(false, $e->getMessage(), null, 500);
                    }
                }
            }
            EOT;

        $controllerContent = str_replace('__namePascalCase__', $name, $controllerContent);
        $controllerContent = str_replace('__nameCamelCase__', Str::camel($name), $controllerContent);
        $controllerContent = str_replace('__nameSnakeCase__', Str::snake($name), $controllerContent);
        $controllerContent = str_replace('__nameSnakeCasePlurals__', Str::snake(Str::plural($name)), $controllerContent);
        $controllerContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $controllerContent);
        $controllerContent = str_replace('__nameKebabCase__', Str::kebab($name), $controllerContent);
        $controllerContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $controllerContent);
        $controllerContent = str_replace('__nameTitleCase__', ucfirst(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name)), $controllerContent);
        file_put_contents($controllerPath, $controllerContent);
    }

    protected function createResource()
    {
        $name = $this->argument('name');
        $this->call('make:resource', ['name' => "{$name}Resource"]);

        $resourcePath = app_path("Http/Resources/{$name}Resource.php");
        $resourceContent = "<?php\n\n";
        $resourceContent .= "namespace App\Http\Resources;\n\n";
        $resourceContent .= "use Illuminate\Http\Resources\Json\JsonResource;\n\n";
        $resourceContent .= "class {$name}Resource extends JsonResource\n";
        $resourceContent .= "{\n";
        $resourceContent .= "    /**\n";
        $resourceContent .= "     * Transform the resource into an array.\n";
        $resourceContent .= "     *\n";
        $resourceContent .= "     * @param  \Illuminate\Http\Request  \$request\n";
        $resourceContent .= "     * @return array<string, mixed>\n";
        $resourceContent .= "     */\n";
        $resourceContent .= "    public function toArray(\$request)\n";
        $resourceContent .= "    {\n";
        $resourceContent .= "        return [\n";
        $resourceContent .= "            // Add your resource here\n";
        $resourceContent .= "        ];\n";
        $resourceContent .= "    }\n";
        $resourceContent .= "}\n";

        file_put_contents($resourcePath, $resourceContent);
    }

    protected function modifyMigration()
    {
        $name = $this->argument('name');
        $name = Str::snake($name);
        $name = Str::plural($name);
        $migration = database_path('migrations/'.date('Y_m_d_His').'_create_'.$name.'_table.php');

        $migrationContent = "<?php\n\n";
        $migrationContent .= "use Illuminate\Database\Migrations\Migration;\n";
        $migrationContent .= "use Illuminate\Database\Schema\Blueprint;\n";
        $migrationContent .= "use Illuminate\Support\Facades\Schema;\n\n";
        $migrationContent .= "return new class extends Migration\n";
        $migrationContent .= "{\n";
        $migrationContent .= "    /**\n";
        $migrationContent .= "     * Run the migrations.\n";
        $migrationContent .= "     */\n";
        $migrationContent .= "    public function up()\n";
        $migrationContent .= "    {\n";
        $migrationContent .= "        Schema::create('{$name}', function (Blueprint \$table) {\n";
        $migrationContent .= "            \$table->uuid('id')->primary();\n";
        $migrationContent .= "            // Add your columns here\n";
        $migrationContent .= "            \$table->softDeletes();\n";
        $migrationContent .= "            \$table->timestamps();\n";
        $migrationContent .= "        });\n";
        $migrationContent .= "    }\n\n";
        $migrationContent .= "    /**\n";
        $migrationContent .= "     * Reverse the migrations.\n";
        $migrationContent .= "     */\n";
        $migrationContent .= "    public function down()\n";
        $migrationContent .= "    {\n";
        $migrationContent .= "        Schema::dropIfExists('{$name}');\n";
        $migrationContent .= "    }\n";
        $migrationContent .= "};\n";

        file_put_contents($migration, $migrationContent);
    }

    protected function modifyRepository()
    {
        $name = $this->argument('name');

        if (! file_exists(app_path('Interfaces'))) {
            mkdir(app_path('Interfaces'), 0777, true);
        }
        if (! file_exists(app_path('Repositories'))) {
            mkdir(app_path('Repositories'), 0777, true);
        }

        $interfacePath = app_path("Interfaces/{$name}RepositoryInterface.php");
        $repositoryPath = app_path("Repositories/{$name}Repository.php");

        $interfaceContent = "<?php\n\nnamespace App\Interfaces;\n\ninterface {$name}RepositoryInterface\n{\n    //\n}\n";

        $interfaceContent =
            <<<'EOT'
            <?php

            namespace App\Interfaces;

            interface __namePascalCase__RepositoryInterface
            {
                public function getAll(
                    ?string $search,
                    ?int $limit,
                    bool $execute
                );

                public function getAllPaginated(
                    ?string $search,
                    int $rowsPerPage
                );

                public function getAllActive(
                string $includeId = null
                );

                public function getById(
                    string $id,
                    bool $withTrashed = false
                );

                public function create(array $data);

                public function update(array $data, string $id);

                public function delete(string $id);
            }
            EOT;

        $interfaceContent = str_replace('__namePascalCase__', $name, $interfaceContent);
        $interfaceContent = str_replace('__namePascalCasePlurals__', Str::studly(Str::plural($name)), $interfaceContent);
        $interfaceContent = str_replace('__nameCamelCase__', Str::camel($name), $interfaceContent);
        $interfaceContent = str_replace('__nameSnakeCase__', Str::snake($name), $interfaceContent);
        $interfaceContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $interfaceContent);
        $interfaceContent = str_replace('__nameKebabCase__', Str::kebab($name), $interfaceContent);
        $interfaceContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $interfaceContent);

        $repositoryContent = "<?php\n\nnamespace App\Repositories;\n\nuse App\Interfaces\\{$name}RepositoryInterface;\n\nclass {$name}Repository implements {$name}RepositoryInterface\n{\n    //\n}\n";

        $repositoryContent = <<<'EOT'
            <?php

            namespace App\Repositories;

            use App\Interfaces\__namePascalCase__RepositoryInterface;
            use App\Models\__namePascalCase__;
            use Illuminate\Support\Facades\DB;

            class __namePascalCase__Repository implements __namePascalCase__RepositoryInterface
            {
                public function getAll(
                    ?string $search = null,
                    ?int $limit = null,
                    bool $execute = true
                )
                {
                    $query = __namePascalCase__::query();

                    if ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    }

                    if ($limit) {
                        $query->limit($limit);
                    }

                    return $execute ? $query->get() : $query;
                }

                public function getAllPaginated(
                    ?string $search = null,
                    int $rowsPerPage = 15
                )
                {
                    $query = __namePascalCase__::query();

                    if ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    }

                    return $query->paginate($rowsPerPage);
                }

                public function getAllActive(string $includeId = null)
                {
                    $query = __namePascalCase__::where('is_active', true);

                    if ($includeId) {
                        $query->orWhere(function ($subQuery) use ($includeId) {
                            $subQuery->withTrashed()->where('id', '=', $includeId);
                        });
                    }

                    return $query->get();
                }

                public function getById(string $id, bool $withTrashed = false)
                {
                    $query = __namePascalCase__::where('id', '=', $id);

                    if ($withTrashed) {
                        $query->withTrashed();
                    }

                    return $query->first();
                }

                public function isAvailable(string $id): bool
                {
                    return __namePascalCase__::where('id', '=', $id)
                        ->where('is_active', true)
                        ->exists();
                }

                public function create(array $data)
                {
                    DB::beginTransaction();

                    try {
                        $__nameCamelCase__ = __namePascalCase__::create($data);

                        DB::commit();

                        return $__nameCamelCase__;
                    } catch (Exception $e) {
                        DB::rollBack();
                        throw new Exception($e->getMessage());
                    }
                }

                public function update(array $data, string $id)
                {
                    DB::beginTransaction();

                    try {
                        $__nameCamelCase__ = __namePascalCase__::findOrFail($id);
                        $__nameCamelCase__->update($data);

                        DB::commit();

                        return $__nameCamelCase__;
                    } catch (Exception $e) {
                        DB::rollBack();
                        throw new Exception($e->getMessage());
                    }
                }

                public function delete(string $id)
                {
                    DB::beginTransaction();

                    try {
                        $__nameCamelCase__ = __namePascalCase__::findOrFail($id);
                        $__nameCamelCase__->delete();

                        DB::commit();

                        return $__nameCamelCase__;
                    } catch (Exception $e) {
                        DB::rollBack();
                        throw new Exception($e->getMessage());
                    }
                }

            }
            EOT;

        $repositoryContent = str_replace('__namePascalCase__', $name, $repositoryContent);
        $repositoryContent = str_replace('__namePascalCasePlurals__', Str::studly(Str::plural($name)), $repositoryContent);
        $repositoryContent = str_replace('__nameCamelCase__', Str::camel($name), $repositoryContent);
        $repositoryContent = str_replace('__nameSnakeCase__', Str::snake($name), $repositoryContent);
        $repositoryContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $repositoryContent);
        $repositoryContent = str_replace('__nameKebabCase__', Str::kebab($name), $repositoryContent);
        $repositoryContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $repositoryContent);

        file_put_contents($interfacePath, $interfaceContent);
        file_put_contents($repositoryPath, $repositoryContent);

        if (! file_exists(app_path('Providers/RepositoryServiceProvider.php'))) {
            touch(app_path('Providers/RepositoryServiceProvider.php'));

            $repositoryServiceProviderContent =
                <<<'EOT'
                <?php

                namespace App\Providers;

                use Illuminate\Support\ServiceProvider;

                class RepositoryServiceProvider extends ServiceProvider
                {
                    /**
                     * Register services.
                     *
                     * @return void
                     */
                    public function register()
                    {

                    }

                    /**
                     * Bootstrap services.
                     *
                     * @return void
                     */
                    public function boot()
                    {
                        //
                    }
                }
                EOT;
            file_put_contents(app_path('Providers/RepositoryServiceProvider.php'), $repositoryServiceProviderContent);
        }

        $repositoryServiceProvider = app_path('Providers/RepositoryServiceProvider.php');
        $repositoryServiceProviderContent = file_get_contents($repositoryServiceProvider);

        $replacement = <<<PHP
\$this->app->bind(\App\Interfaces\\{$name}RepositoryInterface::class, \App\Repositories\\{$name}Repository::class);
    }\n
PHP;

        $repositoryServiceProviderContent = preg_replace('/public function register\(\)\s*{([^}]*)}/s', "public function register() {\n$1$replacement", $repositoryServiceProviderContent, 1);

        file_put_contents($repositoryServiceProvider, $repositoryServiceProviderContent);
    }

    protected function createTest()
    {
        $name = $this->argument('name');
        $test = base_path("tests/Feature/{$name}APITest.php");
        $testContent =
            <<<'EOT'
            <?php

            namespace Tests\Feature;

            use App\Enum\UserRoleEnum;
            use App\Models\User;
            use App\Models\__namePascalCase__;
            use Illuminate\Support\Arr;
            use Illuminate\Support\Facades\Storage;
            use Illuminate\Support\Str;
            use Tests\TestCase;

            class __namePascalCase__APITest extends TestCase
            {
                public function setUp(): void
                {
                    parent::setUp();

                    Storage::fake('public');
                }

                // 1-1
                public function test___nameSnakeCase___api_call_index_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCasePlurals__ = __namePascalCase__::factory(3)->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__');

                    $response->assertSuccessful();

                    $resultCount = 0;
                    foreach ($response['data'] as $data) {
                        foreach ($__nameCamelCasePlurals__ as $__nameCamelCase__) {
                            if ($data['id'] == $__nameCamelCase__->id) {
                                $resultCount++;
                            }
                        }
                    }
                    $this->assertEquals($resultCount, count($__nameCamelCasePlurals__));
                }

                // 1-2-1
                public function test___nameSnakeCase___api_call_get_all_active_without_param_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active');

                    $response->assertSuccessful();
                }

                // 1-2-2
                public function test___nameSnakeCase___api_call_get_all_active_with_existing_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => $__nameCamelCase__->id]);

                    $response->assertSuccessful();

                    $responseHas__namePascalCase__ = false;
                    foreach ($response['data'] as $data) {
                        if ($data['id'] == $__nameCamelCase__->id) {
                            $responseHas__namePascalCase__ = true;
                        }
                    }
                    $this->assertTrue($responseHas__namePascalCase__);

                    // Inactive
                    $__nameCamelCase__ = __namePascalCase__::factory()->create([
                        'is_active' => false
                    ]);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => $__nameCamelCase__->id]);

                    $response->assertSuccessful();

                    $responseHas__namePascalCase__ = false;
                    foreach ($response['data'] as $data) {
                        if ($data['id'] == $__nameCamelCase__->id) {
                            $responseHas__namePascalCase__ = true;
                        }
                    }
                    $this->assertTrue($responseHas__namePascalCase__);

                    // Soft Deleted
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => $__nameCamelCase__->id]);

                    $response->assertSuccessful();

                    $__nameCamelCase__->delete();

                    $responseHas__namePascalCase__ = false;
                    foreach ($response['data'] as $data) {
                        if ($data['id'] == $__nameCamelCase__->id) {
                            $responseHas__namePascalCase__ = true;
                        }
                    }
                    $this->assertTrue($responseHas__namePascalCase__);
                }

                // 1-2-3
                public function test___nameSnakeCase___api_call_get_all_active_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => 0]);

                    $this->assertNotEquals(200, $response->getStatusCode());

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => -1]);

                    $this->assertNotEquals(200, $response->getStatusCode());

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/read/all-active', ['include_id' => Str::random(5)]);

                    $this->assertNotEquals(200, $response->getStatusCode());
                }

                // 1-3-1
                public function test___nameSnakeCase___api_call_show_with_valid_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id);

                    $response->assertSuccessful();

                    $__nameCamelCase__ = Arr::except($__nameCamelCase__->toArray(), ['created_at', 'updated_at']);

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);
                }

                // 1-3-2
                public function test___nameSnakeCase___api_call_show_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/0');

                    $response->assertStatus(404);
                }

                // 1-4-1
                public function test___nameSnakeCase___api_call_check_availability_with_valid_param_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->create(
                        ['is_active' => true]
                    );

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/'.$__nameCamelCase__->id);

                    $response->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => true]);

                    // Inactive
                    $__nameCamelCase__ = __namePascalCase__::factory()->create(
                        ['is_active' => false]
                    );

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/'.$__nameCamelCase__->id);

                    $response->assertStatus(404);

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => false]);
                }

                // 1-4-2
                public function test___nameSnakeCase___api_call_check_availability_with_invalid_param_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/'.Str::random(5));

                    $response->assertStatus(404);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/0');

                    $response->assertStatus(404);

                    $response = $this->json('GET', 'api/v1/__nameKebabCase__/check-availability/-1');

                    $response->assertStatus(404);
                }

                // 2-1-1
                public function test___nameSnakeCase___api_call_create_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);
                }

                // 2-1-2
                public function test___nameSnakeCase___api_call_create_with_existing_code_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $existing__namePascalCase__ = __namePascalCase__::factory()->create();

                    $new__namePascalCase__ = __namePascalCase__::factory()->make([
                        'code' => $existing__namePascalCase__->code,
                    ])->toArray();

                    $response = $this->json('POST', '/api/v1/__nameKebabCase__', $new__namePascalCase__);

                    $response->assertStatus(422);
                }

                // 2-1-3
                public function test___nameSnakeCase___api_call_create_without_required_fields_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Code
                    $__nameCamelCase__ = __namePascalCase__::factory()->make(['code' => null])->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertStatus(422);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__['code']);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertStatus(422);
                }

                // 2-1-4
                public function test___nameSnakeCase___api_call_create_without_nullable_fields_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Remarks
                    $__nameCamelCase__ = __namePascalCase__::factory()->make(['remarks' => null])->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__['remarks']);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);

                    // Is Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->make(['is_active' => null])->toArray();

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $__nameCamelCase__['is_active'] = true;

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__['is_active']);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertSuccessful();

                    $__nameCamelCase__['is_active'] = true;

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__);
                }

                // 2-1-5
                public function test___nameSnakeCase___api_call_create_with_empty_array_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = [];

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__', $__nameCamelCase__);

                    $api->assertStatus(422);
                }

                // 3-1-1
                public function test___nameSnakeCase___api_call_update_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__Update);
                }

                // 3-1-2
                public function test___nameSnakeCase___api_call_update_with_existing_code_in_same___nameSnakeCase___with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $existing__namePascalCase__ = __namePascalCase__::factory()->create();

                    $new__namePascalCase__ = __namePascalCase__::factory()->make([
                        'code' => $existing__namePascalCase__->code,
                    ])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$existing__namePascalCase__->id, $new__namePascalCase__);

                    $response->assertSuccessful();

                    $new__namePascalCase__ = Arr::except($new__namePascalCase__, ['created_at', 'updated_at']);
                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $new__namePascalCase__);
                }

                // 3-1-3
                public function test___nameSnakeCase___api_call_update_with_existing_code_in_another___nameSnakeCase___with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $existing__namePascalCase__ = __namePascalCase__::factory()->create();

                    $new__namePascalCase__ = __namePascalCase__::factory()->create();

                    $update__namePascalCase__ = __namePascalCase__::factory()->make([
                        'code' => $existing__namePascalCase__->code,
                    ]);

                    $api = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$new__namePascalCase__->id, $update__namePascalCase__->toArray());

                    $api->assertStatus(422);
                }

                // 3-1-4
                public function test___nameSnakeCase___api_call_update_without_required_fields_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Code
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make(['code' => null])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__Update['code']);

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);

                    // Is Active
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make(['is_active' => null])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__Update['is_active']);

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertStatus(422);
                }

                // 3-1-5
                public function test___nameSnakeCase___api_call_update_without_nullable_fields_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    // Remarks
                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make(['remarks' => null])->toArray();

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertSuccessful();

                    $__nameCamelCase__Update = Arr::except($__nameCamelCase__Update, ['created_at', 'updated_at']);
                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__Update);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $__nameCamelCase__Update = __namePascalCase__::factory()->make()->toArray();
                    unset($__nameCamelCase__Update['remarks']);

                    $response = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, $__nameCamelCase__Update);

                    $response->assertSuccessful();

                    $__nameCamelCase__Update = Arr::except($__nameCamelCase__Update, ['created_at', 'updated_at']);
                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', $__nameCamelCase__Update);
                }

                // 3-1-6
                public function test___nameSnakeCase___api_call_update_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->make()->toArray();

                    $api = $this->json('PUT', 'api/v1/__nameKebabCase__/'. Str::random(5), $__nameCamelCase__);

                    $api->assertStatus(404);
                }

                // 3-1-7
                public function test___nameSnakeCase___api_call_update_with_empty_array_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $api = $this->json('PUT', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id, []);

                    $api->assertStatus(422);
                }

                // 3-2-1
                public function test___nameSnakeCase___api_call_update_active_status_with_valid_param_and_valid_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create([
                        'is_active' => true
                    ]);

                    // Active
                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => true]);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => true]);

                    // Inactive
                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => false]);

                    $api->assertSuccessful();

                    $this->assertDatabaseHas('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id, 'is_active' => false]);
                }

                // 3-2-2
                public function test___nameSnakeCase___api_call_update_active_status_with_valid_param_and_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.Str::random(5), ['is_active' => mt_rand(0, 1)]);

                    $api->assertStatus(404);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/0', ['is_active' => mt_rand(0, 1)]);

                    $api->assertStatus(404);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/-1', ['is_active' => mt_rand(0, 1)]);

                    $api->assertStatus(404);
                }

                // 3-2-3
                public function test___nameSnakeCase___api_call_update_active_status_with_invalid_param_and_valid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create([
                        'is_active' => true
                    ]);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => null]);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => '']);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.$__nameCamelCase__->id, ['is_active' => Str::random(5)]);

                    $api->assertStatus(422);
                }

                // 3-2-4
                public function test___nameSnakeCase___api_call_update_active_status_with_invalid_param_and_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/'.Str::random(5), ['is_active' => null]);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/0', ['is_active' => '']);

                    $api->assertStatus(422);

                    $api = $this->json('POST', 'api/v1/__nameKebabCase__/active/1', ['is_active' => Str::random(5)]);

                    $api->assertStatus(422);
                }

                // 4-1
                public function test___nameSnakeCase___api_call_delete_with_valid_id_with_super_admin_user_expect_success()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $__nameCamelCase__ = __namePascalCase__::factory()->create();

                    $response = $this->json('DELETE', 'api/v1/__nameKebabCase__/'.$__nameCamelCase__->id);

                    $response->assertSuccessful();

                    $this->assertSoftDeleted('__nameSnakeCasePlurals__', ['id' => $__nameCamelCase__->id]);
                }

                // 4-2
                public function test___nameSnakeCase___api_call_delete_with_invalid_id_with_super_admin_user_expect_fail()
                {
                    $user = User::factory()->create()->assignRole(UserRoleEnum::OWNER->value);

                    $this->actingAs($user);

                    $response = $this->json('DELETE', 'api/v1/__nameKebabCase__/'.Str::random(5));

                    $response->assertStatus(404);
                }
            }
            EOT;
        $testContent = str_replace('@name', $name.'Test', $testContent);
        $testContent = str_replace('__namePascalCase__', $name, $testContent);
        $testContent = str_replace('__namePascalCasePlurals__', Str::studly(Str::plural($name)), $testContent);
        $testContent = str_replace('__nameCamelCase__', Str::camel($name), $testContent);
        $testContent = str_replace('__nameSnakeCase__', Str::snake($name), $testContent);
        $testContent = str_replace('__nameSnakeCasePlurals__', Str::snake(Str::plural($name)), $testContent);
        $testContent = str_replace('__nameProperCase__', ucfirst(strtolower(preg_replace('/(?<=\\w)(?=[A-Z])/', ' ', $name))), $testContent);
        $testContent = str_replace('__nameKebabCase__', Str::kebab($name), $testContent);
        $testContent = str_replace('__nameCamelCasePlurals__', Str::camel(Str::plural($name)), $testContent);

        file_put_contents($test, $testContent);
    }

    protected function createFactory()
    {
        $name = $this->argument('name');
        $factory = database_path("factories/{$name}Factory.php");

        $factoryContent = "<?php\n\n";
        $factoryContent .= "namespace Database\Factories;\n\n";
        $factoryContent .= "use Illuminate\Database\Eloquent\Factories\Factory;\n";
        $factoryContent .= "use Illuminate\Support\Str;\n\n";
        $factoryContent .= "class {$name}Factory extends Factory\n";
        $factoryContent .= "{\n";
        $factoryContent .= "    /**\n";
        $factoryContent .= "     * Define the model's default state.\n";
        $factoryContent .= "     *\n";
        $factoryContent .= "     * @return array<string, mixed>\n";
        $factoryContent .= "     */\n";
        $factoryContent .= "    public function definition(): array\n";
        $factoryContent .= "    {\n";
        $factoryContent .= "        return [\n";
        $factoryContent .= "            // Define your default state here\n";
        $factoryContent .= "        ];\n";
        $factoryContent .= "    }\n";
        $factoryContent .= "}\n";

        file_put_contents($factory, $factoryContent);
    }
}
