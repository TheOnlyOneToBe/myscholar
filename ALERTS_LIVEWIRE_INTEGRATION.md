# Alert System + Livewire Integration Guide

Un guide complet pour intégrer le système d'alertes avec Livewire et afficher les toasts en JavaScript.

## 🚀 Architecture d'Intégration

```
AlertService (Backend)
    ↓
    ├─ Stockage en Session
    ├─ Injection API (Middleware)
    └─ Livewire Components
         ↓
AlertToast (Livewire Component)
    ↓
    Bootstrap Toasts + Auto-dismiss (5s)
         ↓
JavaScript Display (Smooth animations)
```

## 📦 Composants Créés

### 1. Livewire AlertToast Component
**File**: `app/Livewire/Components/AlertToast.php`

Récupère les alertes du service et les affiche via la view Blade.

```php
public function mount(AlertService $alertService)
{
    $this->alerts = $alertService->all();
}
```

### 2. Alert Toast View
**File**: `resources/views/livewire/components/alert-toast.blade.php`

- Affiche les 3 types d'alertes (success, warning, error)
- Bootstrap toast styling
- Animations CSS fluides
- Auto-dismiss après 5 secondes
- Boutons de fermeture manuels

### 3. Application Layout
**File**: `resources/views/layouts/app.blade.php`

Layout principal avec:
- Navigation
- Composant AlertToast enregistré
- Scripts Bootstrap et Livewire
- Gestion auto-dismiss

## 💻 Utilisation dans les Contrôleurs Livewire

### Exemple Simple avec Trait HasAlerts

```php
use App\Livewire\Component;
use App\Traits\HasAlerts;

class UserForm extends Component
{
    use HasAlerts;

    public function mount(): void
    {
        $this->initializeAlerts();
    }

    public function submit(): void
    {
        // Validation et traitement...

        $this->success('User created successfully', 'USER_CREATED');
        $this->warning('Email verification required', 'EMAIL_VERIFY');

        $this->reset();
    }

    public function render()
    {
        return view('livewire.forms.user-form');
    }
}
```

### Exemple Avancé avec Service Injection

```php
use App\Services\AlertService;

class ProductForm extends Component
{
    public function __construct(
        protected AlertService $alerts
    ) {}

    public function save(): void
    {
        try {
            // Créer produit...
            $this->alerts->success('Product saved', 'PRODUCT_SAVED');
        } catch (Exception $e) {
            $this->alerts->error('Error: ' . $e->getMessage(), 'SAVE_ERROR');
        }
    }
}
```

## 🎯 Utilisation dans les Contrôleurs Traditionnels

### Helper Globaux

```php
public function store(Request $request)
{
    $user = User::create($request->validated());

    alert_success('User created', 'USER_CREATED');
    alert_warning('Please verify email', 'EMAIL_VERIFY');

    return redirect('/users')->with('message', 'Success');
}
```

### Service Injection

```php
public function update(Request $request, AlertService $alerts)
{
    $user = User::find($request->id);
    $user->update($request->validated());

    $alerts->success('Profile updated', 'PROFILE_UPDATED');
    $alerts->warning('Changes require re-login', 'RELOGIN_REQUIRED');

    return redirect()->back();
}
```

## 🔄 Flux des Données

### 1. Backend → Session
```php
// Dans le contrôleur
alert_success('Message', 'CODE');

// Stocké en session automatiquement
Session::put('_alerts', [...]);
```

### 2. Session → Livewire
```php
// Dans AlertToast::mount()
$this->alerts = $alertService->all(); // Depuis la session
```

### 3. Livewire → View Blade
```blade
@foreach($alerts['success'] as $alert)
    <div class="toast success-toast">
        {{ $alert['message'] }}
    </div>
@endforeach
```

### 4. View → JavaScript Display
```javascript
// Auto-display avec animation
const bsToast = new bootstrap.Toast(toastElement, {
    autohide: true,
    delay: 5000
});
bsToast.show();
```

## 📤 API Response Integration

Les alertes sont automatiquement injectées dans les réponses JSON:

```json
{
  "data": {...},
  "alerts": {
    "success": [
      {
        "message": "Operation successful",
        "code": "OP_SUCCESS",
        "id": "507f1f77bcf86cd799439011"
      }
    ],
    "warning": [],
    "error": []
  }
}
```

### Frontend (Vue.js/React)

```javascript
const response = await fetch('/api/users', { method: 'POST' });
const data = await response.json();

if (data.alerts) {
  // Envoyer les alertes au composant toast
  this.$emit('show-alerts', data.alerts);
}
```

## 🎨 Affichage des Toasts

### Bootstrap Toast Styling

- **Success**: Fond vert, bordure gauche verte
- **Warning**: Fond jaune, bordure gauche jaune
- **Error**: Fond rouge, bordure gauche rouge

### Animations CSS

```css
@keyframes slideInRight {
    from {
        transform: translateX(400px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.toast {
    animation: slideInRight 0.3s ease-out;
}
```

## 🧪 Tests d'Intégration

### Routes de Test
- `GET /test/alerts` - Page de démo
- `POST /test/alert-success` - Déclenche succès
- `POST /test/alert-warning` - Déclenche avertissement
- `POST /test/alert-error` - Déclenche erreur
- `POST /test/alert-multiple` - Déclenche multiples

### Formulaire Livewire de Test
```php
use App\Livewire\Forms\SimpleForm;

// Voir resources/views/livewire/forms/simple-form.blade.php
// Test complet du système avec validation
```

## 🔌 Modules Intégrés

### Notifications Module
```php
// NotificationController.php
public function markAsRead(Notification $notification)
{
    $notification->markAsRead();
    alert_success(__('notifications.messages.marked_as_read'), 
                  'NOTIFICATION_READ');
    return response()->json([...]);
}
```

### NotificationActionController.php
```php
public function approve(Notification $notification)
{
    // ...
    alert_success(__('notifications.messages.action_approved'), 
                  'ACTION_APPROVED');
    return response()->json([...]);
}
```

## 📋 Checklist d'Intégration

- ✅ AlertService enregistré en service provider
- ✅ AlertBag pour gestion des alertes
- ✅ Middleware AddAlertsToResponse
- ✅ Helpers globaux (alert_success, etc)
- ✅ Trait HasAlerts pour contrôleurs
- ✅ Composant Livewire AlertToast
- ✅ View Blade avec Bootstrap toasts
- ✅ Layout avec AlertToast intégré
- ✅ Exemple Livewire Component (SimpleForm)
- ✅ Exemple Contrôleur Traditionnel (TestAlertController)
- ✅ Routes de test
- ✅ JavaScript auto-dismiss (5s)
- ✅ Support Livewire navigation

## 🚀 Cas d'Usage Pratiques

### 1. Créer Utilisateur
```php
public function store(StoreUserRequest $request)
{
    $user = User::create($request->validated());
    
    alert_success(
        __('users.messages.created', ['name' => $user->full_name]),
        'USER_CREATED'
    );
    
    return redirect('/users');
}
```

### 2. Supprimer avec Confirmation
```php
public function destroy(User $user)
{
    $user->delete();
    
    alert_success(
        __('users.messages.deleted'),
        'USER_DELETED'
    );
    
    return response()->json(['success' => true]);
}
```

### 3. Validation Multiple
```php
public function validate(ValidateRequest $request)
{
    $errors = [];
    
    if (condition1) {
        alert_error('Error 1', 'ERROR_1');
        $errors[] = 'error1';
    }
    
    if (condition2) {
        alert_error('Error 2', 'ERROR_2');
        $errors[] = 'error2';
    }
    
    if ($errors) {
        return response()->json(['errors' => $errors], 422);
    }
    
    return response()->json(['valid' => true]);
}
```

### 4. Opérations Longues
```php
public function processLargeFile(UploadRequest $request)
{
    // Traiter...
    alert_success('File processed', 'FILE_PROCESSED');
    alert_warning('Indexing in progress', 'INDEXING');
    alert_warning('This may take a few minutes', 'WAIT_NOTICE');
    
    return response()->json(['data' => $result]);
}
```

## 📱 Frontend avec Vue.js/React

### Vue.js
```vue
<template>
    <div v-if="alerts" class="alert-container">
        <div v-for="alert in alerts.success" :key="alert.id" class="toast success">
            {{ alert.message }}
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return { alerts: null };
    },
    methods: {
        async submitForm() {
            const response = await fetch('/api/submit', { method: 'POST' });
            const data = await response.json();
            this.alerts = data.alerts;
        }
    }
}
</script>
```

### React
```jsx
function App() {
    const [alerts, setAlerts] = useState(null);

    const submitForm = async () => {
        const response = await fetch('/api/submit', { method: 'POST' });
        const data = await response.json();
        setAlerts(data.alerts);
    };

    return (
        <div className="alert-container">
            {alerts?.success?.map(alert => (
                <div key={alert.id} className="toast success">
                    {alert.message}
                </div>
            ))}
        </div>
    );
}
```

## 🔧 Personnalisation

### Changer le délai d'auto-dismiss
```blade
{{-- Dans alert-toast.blade.php --}}
<div class="toast" data-bs-delay="10000">
    {{-- 10 secondes --}}
</div>
```

### Ajouter des icônes personnalisées
```blade
<div class="toast-header bg-custom">
    <i class="fas fa-check-circle me-2"></i>
    {{ $alert['message'] }}
</div>
```

### Animations personnalisées
```css
@keyframes slideInLeft {
    from {
        transform: translateX(-400px);
        opacity: 0;
    }
}

.toast.custom-animation {
    animation: slideInLeft 0.3s ease-out;
}
```

## ✅ Résumé

Le système d'alertes est maintenant **pleinement intégré** avec:
- ✅ Livewire Components
- ✅ Bootstrap Toasts
- ✅ Auto-dismiss JavaScript
- ✅ Tous les modules (Notifications, Auth, Config, Audit)
- ✅ Contrôleurs traditionnels et Livewire
- ✅ Helpers globaux
- ✅ Traits réutilisables
- ✅ Documentation complète

**À utiliser partout pour les confirmations, succès, et erreurs!**
