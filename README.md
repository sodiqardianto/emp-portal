# Bethsaida Employee Portal (New)

Rewrite dari Employee Portal CI3/Express ke Laravel 12.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2+
- **Frontend:** Blade + Bootstrap 5 + Vite
- **Database:** SQL Server (cross-database: `BackOffice`, `UM`)
- **Icons:** Bootstrap Icons, Font Awesome 7

## Setup

```bash
composer setup
```

Atau manual:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

## Development

```bash
composer dev
```

Menjalankan server, queue, logs, dan Vite secara bersamaan.

## Testing

```bash
composer test
```

---

## Arsitektur & Konvensi

Project ini menggunakan **Repository Pattern** dengan layer yang jelas. Semua developer wajib mengikuti struktur ini.

### Struktur Folder

```
app/
├── Models/              → Eloquent models (tabel, relasi, scopes)
├── Repositories/        → Query & akses database
├── Services/            → Business logic
├── Concerns/            → Shared traits (NormalizesStrings, dll)
├── Http/
│   ├── Controllers/     → Thin controller, routing ke service
│   ├── Requests/        → Validasi input (FormRequest)
│   ├── Resources/       → Format output JSON (JsonResource)
│   └── Middleware/      → Auth & permission checks
└── View/
    └── Composers/       → Data injection ke Blade views
```

### Aturan Per Layer

| Layer | Tanggung Jawab | Tidak Boleh |
|-------|---------------|-------------|
| **Controller** | Terima request, panggil service, return response/view | Query DB, business logic |
| **Service** | Business logic, validasi bisnis, orchestrate repository | Langsung query DB, akses HTTP request |
| **Repository** | Query database (Eloquent/Query Builder) | Business logic, akses request/session |
| **Model** | Definisi tabel, relasi, scopes, accessors | Business logic berat |
| **FormRequest** | Validasi input dari user | Business logic |
| **Resource** | Format data untuk JSON response | Query DB, logic |

### Flow Request

```
Request → Controller → Service → Repository → Database
                                      ↓
Response ← Controller ← Service ← Repository
```

Contoh lengkap: lihat modul **Permission** (`PermissionController` → `PermissionService` → `PermissionRepository` → `Permission` model).

### Membuat Modul Baru

1. **Model** — `app/Models/NamaModel.php`
   ```php
   class NamaModel extends Model
   {
       protected $connection = 'employee_sqlsrv';
       protected $table = 'BackOffice.dbo.nama_tabel';
       protected $primaryKey = 'kolom_pk';
       protected $keyType = 'string';
       public $incrementing = false;
       public $timestamps = false;
   }
   ```

2. **Repository** — `app/Repositories/NamaRepository.php`
   ```php
   class NamaRepository
   {
       public function paginate(array $criteria): LengthAwarePaginator
       {
           return NamaModel::query()
               ->when($criteria['search'], fn ($q, $s) => $q->where('nama', 'like', "%{$s}%"))
               ->orderBy($criteria['sortBy'], $criteria['sortDir'])
               ->paginate($criteria['pageSize']);
       }

       public function findByCode(string $code): ?NamaModel
       {
           return NamaModel::where('kode', $code)->first();
       }
   }
   ```

3. **Service** — `app/Services/NamaService.php`
   ```php
   class NamaService
   {
       use NormalizesStrings;

       public function __construct(private readonly NamaRepository $repository) {}

       public function create(array $data, string $actor): void
       {
           // validasi bisnis di sini
           $this->repository->create($data, $actor);
       }
   }
   ```

4. **FormRequest** — `app/Http/Requests/NamaRequest.php`
   ```php
   class NamaRequest extends FormRequest
   {
       public function authorize(): bool { return true; }

       public function rules(): array
       {
           return [
               'nama' => ['required', 'string', 'max:200'],
           ];
       }
   }
   ```

5. **Resource** (jika ada endpoint JSON) — `app/Http/Resources/NamaResource.php`
   ```php
   class NamaResource extends JsonResource
   {
       public function toArray(Request $request): array
       {
           return [
               'kode' => $this->kode,
               'nama' => $this->nama,
           ];
       }
   }
   ```

6. **Controller** — `app/Http/Controllers/NamaController.php`
   ```php
   class NamaController extends Controller
   {
       public function __construct(private readonly NamaService $service) {}

       public function index(): View
       {
           return view('nama.index');
       }

       public function store(NamaRequest $request): RedirectResponse
       {
           $this->service->create($request->validated(), $request->user()->EmployeeCode);
           return redirect()->route('nama.index')->with('success', 'Berhasil disimpan.');
       }
   }
   ```

7. **Route** — `routes/web.php`
   ```php
   Route::prefix('menu/kategori/nama')
       ->middleware(['auth', 'menu.access:/menu/kategori/nama'])
       ->group(function (): void {
           Route::get('/', [NamaController::class, 'index'])->middleware('menu.permission:VIEW');
           Route::post('/', [NamaController::class, 'store'])->middleware('menu.permission:ADD');
       });
   ```

### Hal Penting

- **Jangan sanitize input manual** (regex strip karakter, dll). Laravel parameterized queries sudah aman dari SQL injection.
- **Gunakan `NormalizesStrings` trait** kalau butuh `trimToNull()` — jangan buat helper sendiri.
- **Model tanpa timestamps** — database existing tidak pakai `created_at`/`updated_at` Laravel, set `public $timestamps = false`.
- **Cross-database query** — gunakan full table name di model (`BackOffice.dbo.nama_tabel`), connection di-set di model.
- **Permission** — gunakan middleware `menu.access` dan `menu.permission` untuk proteksi route.
- **Styling** — Bootstrap 5 only. Tidak pakai Tailwind.
- **Semua CSS** di-import dari `resources/css/app.css`. Buat file CSS baru per fitur, lalu import di sana.
