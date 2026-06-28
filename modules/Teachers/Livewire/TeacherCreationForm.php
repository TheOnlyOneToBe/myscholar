<?php

namespace Modules\Teachers\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Modules\Auth\Models\User;
use Modules\Teachers\Models\Teacher;
use Illuminate\Support\Facades\Hash;

#[Layout('layouts.app')]
class TeacherCreationForm extends Component
{
    // Mode: 'new' ou 'existing'
    public $mode = 'new';

    // Utilisateur existant
    public $userId = '';
    public $searchUser = '';

    // Nouveau utilisateur
    public $firstName = '';
    public $lastName = '';
    public $email = '';
    public $username = '';
    public $password = '';
    public $passwordConfirmation = '';
    public $phone = '';

    // Infos enseignant (pour les deux modes)
    public $teacherCode = '';
    public $specialization = '';
    public $qualificationLevel = '';
    public $hireDate = '';
    public $filiere = '';
    public $officeLocation = '';
    public $yearsOfExperience = 0;
    public $bio = '';
    public $phoneOffice = '';
    public $emailOffice = '';

    public $message = '';
    public $messageType = '';
    public $showSuccess = false;

    protected $queryString = ['mode'];

    public function getAvailableUsersProperty()
    {
        $query = User::where('id', '!=', auth()->id())
            ->whereDoesntHave('teacher');

        if ($this->searchUser) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', "%{$this->searchUser}%")
                    ->orWhere('last_name', 'like', "%{$this->searchUser}%")
                    ->orWhere('email', 'like', "%{$this->searchUser}%")
                    ->orWhere('username', 'like', "%{$this->searchUser}%");
            });
        }

        return $query->limit(10)->get();
    }

    public function selectUser($userId)
    {
        $user = User::findOrFail($userId);
        $this->userId = $userId;
        $this->firstName = $user->first_name;
        $this->lastName = $user->last_name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->phone = $user->phone;
        $this->searchUser = '';
    }

    public function clearUserSelection()
    {
        $this->userId = '';
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->username = '';
        $this->phone = '';
        $this->searchUser = '';
    }

    public function generateTeacherCode()
    {
        $year = date('Y');
        $count = Teacher::whereYear('created_at', $year)->count() + 1;
        $this->teacherCode = 'PROF-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function submit()
    {
        // Validations communes
        $commonRules = [
            'teacherCode' => 'required|string|unique:teachers,teacher_code',
            'specialization' => 'required|string|max:255',
            'qualificationLevel' => 'required|string|max:255',
            'hireDate' => 'nullable|date',
            'filiere' => 'nullable|in:generale,technique',
            'officeLocation' => 'nullable|string|max:255',
            'yearsOfExperience' => 'required|integer|min:0',
            'bio' => 'nullable|string|max:1000',
            'phoneOffice' => 'nullable|string|max:20',
            'emailOffice' => 'nullable|email|max:255',
        ];

        // Validations spécifiques au mode
        if ($this->mode === 'new') {
            $rules = array_merge($commonRules, [
                'firstName' => 'required|string|max:255',
                'lastName' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'username' => 'required|string|unique:users,username|min:3|max:255',
                'password' => 'required|string|min:8|confirmed',
                'phone' => 'nullable|string|max:20',
            ]);
        } else {
            $rules = array_merge($commonRules, [
                'userId' => 'required|exists:users,id',
            ]);
        }

        $this->validate($rules, [
            'email.unique' => 'Cet email est déjà utilisé.',
            'username.unique' => 'Ce nom d\'utilisateur est déjà pris.',
            'teacherCode.unique' => 'Ce matricule existe déjà.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        try {
            if ($this->mode === 'new') {
                // Créer un nouvel utilisateur
                $user = User::create([
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'email' => $this->email,
                    'username' => $this->username,
                    'password' => Hash::make($this->password),
                    'phone' => $this->phone,
                    'is_active' => true,
                ]);
                $user->assignRole('enseignant');
            } else {
                // Utiliser l'utilisateur existant
                $user = User::findOrFail($this->userId);
                $user->assignRole('enseignant');
            }

            // Créer le profil enseignant
            Teacher::create([
                'user_id' => $user->id,
                'teacher_code' => $this->teacherCode,
                'specialization' => $this->specialization,
                'qualification_level' => $this->qualificationLevel,
                'hire_date' => $this->hireDate ? \Carbon\Carbon::parse($this->hireDate) : null,
                'filiere' => $this->filiere,
                'office_location' => $this->officeLocation,
                'years_of_experience' => $this->yearsOfExperience,
                'bio' => $this->bio,
                'phone_office' => $this->phoneOffice,
                'email_office' => $this->emailOffice,
                'is_active' => true,
            ]);

            $this->showSuccess = true;
            $this->message = "L'enseignant {$user->first_name} {$user->last_name} a été créé avec succès!";
            $this->messageType = 'success';

            session()->flash('success', $this->message);
            return redirect()->route('teachers.list');
        } catch (\Exception $e) {
            $this->message = 'Erreur lors de la création: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        return view('teachers::livewire.teacher-creation-form', [
            'availableUsers' => $this->availableUsers,
        ]);
    }
}
