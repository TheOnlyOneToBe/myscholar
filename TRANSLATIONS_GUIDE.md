# Guide des Traductions - MyScholar

## Vue d'ensemble

Ce projet utilise un système de traductions multilingues organisé par module. Chaque module contient ses propres fichiers de traduction en français (FR) et anglais (EN).

## Structure des Dossiers

```
modules/
├── Dashboard/
│   └── resources/lang/
│       ├── fr/
│       │   ├── messages.php       # Messages généraux
│       │   ├── validation.php     # Messages de validation (règles)
│       │   ├── exceptions.php     # Messages d'exceptions (erreurs)
│       │   ├── services.php       # Messages des services
│       │   └── views.php          # Messages des vues (UI)
│       └── en/
│           ├── messages.php
│           ├── validation.php
│           ├── exceptions.php
│           ├── services.php
│           └── views.php
├── Students/
│   └── resources/lang/
│       ├── fr/
│       │   ├── messages.php
│       │   ├── validation.php
│       │   ├── exceptions.php
│       │   ├── services.php
│       │   └── views.php
│       └── en/
│           ├── messages.php
│           ├── validation.php
│           ├── exceptions.php
│           ├── services.php
│           └── views.php
└── [Autres modules...]
```

## Fichiers de Traduction

### 1. **messages.php**
Contient les messages généraux du module.

**Exemple d'utilisation:**
```php
// En français
trans('dashboard::messages.dashboard.title')  // → "Tableau de bord"

// En anglais
trans('dashboard::messages.dashboard.title')  // → "Dashboard"
```

### 2. **validation.php**
Contient les messages de validation des formulaires et les règles.

**Exemple d'utilisation:**
```php
// Dans une Request class
'first_name' => 'required|min:2|max:100',
'messages' => [
    'first_name.required' => trans('students::validation.rules.first_name_required'),
    'first_name.min' => trans('students::validation.messages.min'),
]

// Affichage
{{ trans('students::validation.rules.first_name_required') }}
```

### 3. **exceptions.php**
Contient les messages d'erreurs et exceptions.

**Exemple d'utilisation:**
```php
// Dans une Exception
throw new StudentNotFoundException(
    trans('students::exceptions.student_not_found')
);

// Dans un Controller
abort(403, trans('dashboard::exceptions.document_download.unauthorized'));
```

### 4. **services.php**
Contient les messages de traitement des services.

**Exemple d'utilisation:**
```php
// Dans un Service
Log::info(trans('students::services.student_service.creating'));
// ... opération ...
Log::info(trans('students::services.student_service.created_success'));
```

### 5. **views.php**
Contient les messages pour les vues Blade et composants.

**Exemple d'utilisation:**
```blade
<!-- Dans une vue Blade -->
<label>{{ trans('students::views.labels.first_name') }}</label>
<input placeholder="{{ trans('students::views.placeholders.enter_first_name') }}">

<button>{{ trans('students::views.buttons.save') }}</button>

<!-- Affichage d'alerte -->
@if($success)
    <div class="alert">
        {{ trans('students::views.alerts.student_created') }}
    </div>
@endif

<!-- États vides -->
@if($students->isEmpty())
    <p>{{ trans('students::views.empty_states.no_students') }}</p>
@endif
```

## Configuration Locale

### Changer la Locale

```php
// Dans un middleware ou controller
App::setLocale('fr');  // ou 'en'

// Ou via la configuration
config(['app.locale' => 'fr']);
```

### Helper de Traduction

```php
// Tous ces appels sont équivalents
trans('dashboard::messages.dashboard.title')
__('dashboard::messages.dashboard.title')
app('translator')->get('dashboard::messages.dashboard.title')
```

## Utilisation dans Livewire

```php
// Dans un composant Livewire
class StudentForm extends Component
{
    public function mount()
    {
        $this->title = trans('students::messages.students.create');
    }

    public function save()
    {
        // Validation
        $this->validate([
            'first_name' => 'required|min:2',
        ], [
            'first_name.required' => trans('students::validation.rules.first_name_required'),
        ]);

        // Service call
        Log::info(trans('students::services.student_service.creating'));
        
        // Success
        session()->flash('message', trans('students::services.student_service.created_success'));
    }
}
```

## Utilisation dans les Validations (Form Requests)

```php
namespace Modules\Students\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStudentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|min:2|max:100',
            'last_name' => 'required|min:2|max:100',
            'matricule' => 'required|unique:students,matricule',
            'email' => 'nullable|email|unique:students,email',
            'phone' => 'nullable|regex:/^[0-9\-\+]+$/',
        ];
    }

    public function messages()
    {
        return [
            'first_name.required' => trans('students::validation.rules.first_name_required'),
            'first_name.min' => trans('students::validation.messages.min'),
            'first_name.max' => trans('students::validation.messages.max'),
            'last_name.required' => trans('students::validation.rules.last_name_required'),
            'matricule.required' => trans('students::validation.rules.matricule_required'),
            'matricule.unique' => trans('students::validation.rules.matricule_unique'),
            'email.email' => trans('students::validation.messages.email'),
            'phone.regex' => trans('students::validation.messages.regex'),
        ];
    }

    public function authorize()
    {
        return true;
    }
}
```

## Ajouter des Traductions pour un Nouveau Module

### 1. Créer la structure
```bash
mkdir -p modules/NewModule/resources/lang/fr
mkdir -p modules/NewModule/resources/lang/en
```

### 2. Créer les fichiers
```bash
touch modules/NewModule/resources/lang/fr/messages.php
touch modules/NewModule/resources/lang/fr/validation.php
touch modules/NewModule/resources/lang/fr/exceptions.php
touch modules/NewModule/resources/lang/fr/services.php
touch modules/NewModule/resources/lang/fr/views.php

touch modules/NewModule/resources/lang/en/messages.php
touch modules/NewModule/resources/lang/en/validation.php
touch modules/NewModule/resources/lang/en/exceptions.php
touch modules/NewModule/resources/lang/en/services.php
touch modules/NewModule/resources/lang/en/views.php
```

### 3. Enregistrer le chemin dans le ServiceProvider

```php
namespace Modules\NewModule\Providers;

use Illuminate\Support\ServiceProvider;

class NewModuleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Enregistrer les chemins de traduction
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'newmodule');
        
        // Publier les traductions (optionnel)
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/modules/newmodule'),
        ]);
    }
}
```

## Conventions de Nommage

### Clés de Traduction
- Utiliser le snake_case pour les clés
- Grouper les clés par section/catégorie
- Exemple: `dashboard.student.title` ou `validation.rules.first_name_required`

### Fichiers
- **messages.php**: Messages généraux, titres, descriptions
- **validation.php**: Messages de validation et règles
- **exceptions.php**: Erreurs et messages d'exception
- **services.php**: Messages de processus/actions
- **views.php**: Boutons, labels, placeholders, alertes, états vides

## Modules avec Traductions Implémentées

- ✅ **Dashboard** (FR + EN)
  - messages, validation, exceptions, services, views
  
- ✅ **Students** (FR + EN)
  - messages, validation, exceptions, services, views

## Modules à Ajouter des Traductions

- [ ] Config
- [ ] Auth
- [ ] Grades
- [ ] Attendance
- [ ] Classes
- [ ] Billing
- [ ] Notifications
- [ ] Reporting
- [ ] Audit

## Bonnes Pratiques

1. **Toujours utiliser les traductions**
   - Ne jamais hardcoder les chaînes de caractères en interface
   - Utiliser `trans()` ou `__()` pour tous les textes d'interface

2. **Maintenir la cohérence**
   - Utiliser les mêmes traductions pour les mêmes concepts
   - Exemple: toujours utiliser 'Étudiant' au lieu de 'Élève' de manière aléatoire

3. **Organiser par contexte**
   - Grouper les traductions par fonctionnalité
   - Utiliser des noms de clés descriptifs

4. **Tester les deux langues**
   - Vérifier que les traductions s'affichent correctement
   - Vérifier que les longueurs de texte ne cassent pas le design

5. **Documenter les nouvelles traductions**
   - Ajouter des commentaires pour les traductions complexes
   - Mettre à jour ce guide si nécessaire

## Support des Langues

Actuellement supportées:
- 🇫🇷 Français (fr)
- 🇬🇧 Anglais (en)

Pour ajouter une nouvelle langue:
1. Créer les dossiers `modules/*/resources/lang/new_lang/`
2. Copier et traduire tous les fichiers `.php`
3. Enregistrer la locale dans `config/app.php`

