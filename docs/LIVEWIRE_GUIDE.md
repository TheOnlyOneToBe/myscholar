# Guide Livewire - MyScholar

## Vue d'ensemble

MyScholar utilise **Laravel Livewire** pour créer des interfaces interactives sans JavaScript. Livewire permet de construire des composants réactifs en PHP pur.

## Installation et Configuration

La configuration Livewire se trouve dans `config/livewire.php`.

### Chemin des composants

- **Classe PHP**: `app/Livewire/`
- **Templates Blade**: `resources/views/livewire/`

### Convention de nommage

Composant: `App\Livewire\StudentForm`
Template: `resources/views/livewire/student-form.blade.php`

## Créer un Composant Livewire

### 1. Via commande Artisan

```bash
php artisan make:livewire StudentForm
```

Crée:
- `app/Livewire/StudentForm.php`
- `resources/views/livewire/student-form.blade.php`

### 2. Structure basique

```php
<?php

namespace App\Livewire;

use Livewire\Component;

class StudentForm extends Component
{
    // Propriétés réactives
    public $name = '';
    public $email = '';
    public $filiere = '';
    
    // Règles de validation
    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'filiere' => 'required',
    ];

    // Actions
    public function submit()
    {
        $this->validate();
        
        Student::create([
            'name' => $this->name,
            'email' => $this->email,
            'filiere' => $this->filiere,
        ]);

        session()->flash('message', 'Étudiant créé avec succès!');
    }

    public function render()
    {
        return view('livewire.student-form');
    }
}
```

### 3. Template correspondant

```blade
<!-- resources/views/livewire/student-form.blade.php -->
<form wire:submit="submit" class="space-y-4">
    <div>
        <label>Nom</label>
        <input wire:model="name" type="text" class="form-control">
        @error('name') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Email</label>
        <input wire:model="email" type="email" class="form-control">
        @error('email') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Filière</label>
        <select wire:model="filiere" class="form-control">
            <option value="">Sélectionner</option>
            <option value="SCI">Sciences</option>
            <option value="LIT">Littérature</option>
            <option value="MATH">Mathématiques</option>
        </select>
        @error('filiere') <span class="error">{{ $message }}</span> @enderror
    </div>

    <button type="submit" class="btn btn-primary">Créer</button>
</form>

@if(session()->has('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@endif
```

### 4. Utiliser dans une vue

```blade
<!-- resources/views/students/create.blade.php -->
@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Créer un étudiant</h1>
        <livewire:student-form />
    </div>
@endsection
```

## Propriétés Réactives

Les propriétés publiques sont automatiquement mises à jour quand l'utilisateur les modifie.

```php
class StudentForm extends Component
{
    public $name = '';           // String
    public $active = true;       // Boolean
    public $age = 0;             // Number
    public array $tags = [];     // Array
    public ?Student $student;    // Model
}
```

## Data Binding (Liaison de données)

### One-way binding (du composant vers la view)

```blade
<p>Bienvenue {{ $name }}</p>
```

### Two-way binding (bidirectionnel)

```blade
<input wire:model="name"> <!-- Mise à jour automatique -->
```

### Lazy binding (lors du blur)

```blade
<input wire:model.lazy="name"> <!-- Mise à jour au blur -->
```

### Debounce (délai avant mise à jour)

```blade
<input wire:model.debounce-500ms="searchQuery"> <!-- 500ms délai -->
```

### Throttle (mise à jour maximale chaque X ms)

```blade
<input wire:model.throttle-1000ms="searchQuery"> <!-- Une fois par seconde -->
```

## Actions

### Appeler une méthode du composant

```blade
<button wire:click="delete({{ $id }})">Supprimer</button>
```

```php
public function delete($id)
{
    Student::find($id)->delete();
}
```

### Avec paramètres

```blade
<button wire:click="changeStatus('active')">Activer</button>
```

```php
public function changeStatus($status)
{
    // $status = 'active'
}
```

### Action au changement

```blade
<select wire:change="handleChange">
    <option>Option 1</option>
    <option>Option 2</option>
</select>
```

## Validation

### Validation lors de la soumission

```php
public function submit()
{
    $this->validate();
    // Le reste du code...
}
```

### Validation en temps réel

```php
public function updated($propertyName)
{
    $this->validateOnly($propertyName);
}
```

Ou avec des règles spécifiques:

```php
#[On('updated')] 
public function validateField($propertyName)
{
    $rules = [
        'email' => 'email',
        'name' => 'required|min:3',
    ];
    
    $this->validateOnly($propertyName, [
        $propertyName => $rules[$propertyName] ?? '',
    ]);
}
```

## Cycles de vie

### Mount (initialisation)

```php
public function mount()
{
    $this->name = auth()->user()->name;
}
```

### Updated (après mise à jour d'une propriété)

```php
public function updated($propertyName)
{
    // Appelé quand une propriété change
}

public function updatedName($value)
{
    // Appelé uniquement quand 'name' change
    $this->name = strtoupper($value);
}
```

### Render (avant de rendre le template)

```php
public function render()
{
    return view('livewire.student-form', [
        'students' => Student::all(),
    ]);
}
```

## Événements Livewire

### Dispatcher d'événements

```php
$this->dispatch('studentCreated', studentId: $student->id);
```

### Écouter les événements

```php
#[On('studentCreated')]
public function handleStudentCreated($studentId)
{
    $this->refresh();
}
```

## Intégration avec les Permissions

Voir `docs/PERMISSIONS_AND_ROLES.md` pour l'intégration complète.

### Exemple avec permissions

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\WithLivewirePermissions;

class StudentTable extends Component
{
    use WithLivewirePermissions;

    public function delete($id)
    {
        $this->authorize('students.delete');
        Student::find($id)->delete();
    }

    public function render()
    {
        return view('livewire.student-table', [
            'students' => Student::all(),
            'canDelete' => $this->userCan('students.delete'),
            'canEdit' => $this->userCan('students.edit'),
        ]);
    }
}
```

## Composants imbriqués

### Parent component

```blade
<livewire:student-list />
```

### Child component

```blade
<!-- resources/views/livewire/student-item.blade.php -->
<livewire:student-item :student="$student" />
```

### Communication parent-enfant

Parent → Enfant: Via propriétés

```php
// Parent
class StudentList extends Component
{
    public function render()
    {
        return view('livewire.student-list', [
            'students' => Student::all(),
        ]);
    }
}

// Child (StudentItem.php)
class StudentItem extends Component
{
    public ?Student $student = null;
}
```

Enfant → Parent: Via événements

```php
// Child
$this->dispatch('studentDeleted', id: $this->student->id);

// Parent
#[On('studentDeleted')]
public function refresh()
{
    // Rafraîchir la liste
}
```

## Chargement asynchrone

### Lazy components

```blade
<livewire:heavy-component lazy />
```

### Loading states

```blade
<div wire:loading.delay.longest>
    <p>Chargement...</p>
</div>

<div wire:loading.remove>
    <p>Contenu normal</p>
</div>
```

## Upload de fichiers

### Accepter un fichier

```blade
<input type="file" wire:model="avatar">
```

```php
use Livewire\WithFileUploads;

class UserProfile extends Component
{
    use WithFileUploads;

    public $avatar;

    public function save()
    {
        $this->validate([
            'avatar' => 'image|max:1024',
        ]);

        $this->avatar->store('avatars');
    }
}
```

## Paging/Pagination

### Avec pagination Livewire

```php
use Livewire\WithPagination;

class StudentList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.student-list', [
            'students' => Student::paginate(15),
        ]);
    }
}
```

```blade
<table>
    @foreach($students as $student)
        <tr>
            <td>{{ $student->name }}</td>
        </tr>
    @endforeach
</table>

{{ $students->links() }}
```

## Recherche et filtrage

### Composant de recherche

```php
class StudentSearch extends Component
{
    public $search = '';

    public function render()
    {
        return view('livewire.student-search', [
            'students' => Student::where('name', 'like', '%' . $this->search . '%')
                                 ->get(),
        ]);
    }
}
```

```blade
<input wire:model.debounce-300ms="search" placeholder="Rechercher...">

<ul>
    @foreach($students as $student)
        <li>{{ $student->name }}</li>
    @endforeach
</ul>
```

## Bonnes pratiques

1. **Garder les propriétés publiques** pour ce qui doit être réactif
2. **Utiliser validateOnly()** pour la validation en temps réel
3. **Limiter les données** dans render() - ne pas charger inutilement
4. **Utiliser le debounce** pour les recherches
5. **Tester les composants** - Livewire inclut des helpers de test
6. **Documenter les propriétés publiques** - importante pour la maintenance

## Exemples Complets

Voir `app/Livewire/ExampleComponent.php` pour un exemple complet d'intégration.

## Ressources

- [Documentation officielle Livewire](https://livewire.laravel.com)
- [Composants par défaut](https://livewire.laravel.com/docs/components)
- [Validation](https://livewire.laravel.com/docs/validation)

## Dépannage

### Les mises à jour ne s'affichent pas
- Vérifiez que vous utilisez `wire:model` correctement
- Assurez-vous que la propriété est publique
- Videz le cache: `php artisan cache:clear`

### Les événements ne sont pas reçus
- Vérifiez le nom de l'événement (sensible à la casse)
- Vérifiez l'utilisation de `#[On('eventName')]`

### Performance lente
- Utilisez debounce/throttle pour les inputs
- Chargez moins de données dans render()
- Utilisez les composants lazy pour les contenus lourds
