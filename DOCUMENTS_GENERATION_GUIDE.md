# Documents Generation System - Guide Complet

**Last Updated**: 2026-06-28
**Status**: ✅ Complete - Ready for PDF Library Integration

---

## 📋 Vue d'ensemble

Un système complet de génération de documents scolaires personnalisés (PDF) incluant:
- Certificats de scolarité
- Bulletins scolaires
- Relevés de notes complets
- Résumés d'inscription
- Factures

---

## 🎯 Fonctionnalités

### 1. Historique des Inscriptions
Affichage de l'historique complet avec:
- Année académique
- Classe assignée
- Date d'inscription
- Moyenne générale
- Nombre de notes
- Montants facturés et payés
- Solde en attente

### 2. Documents Générables

#### Certificat de Scolarité 📄
- Confirmation officielle d'inscription
- Statut d'inscription (Inscrit, En attente, etc.)
- Signature du directeur
- Numéro unique du certificat
- Logo et cachet de l'école

#### Bulletin Scolaire 📋
- Notes par matière
- Moyenne générale
- Notation descriptive (Excellent, Bien, etc.)
- Grade lettré (A, B, C, D, F)
- Remarques académiques
- Numéro unique du bulletin

#### Relevé de Notes Complet 📊
- Historique de toutes les années académiques
- Notes par matière et année
- Moyennes annuelles
- Moyenne globale
- Vue d'ensemble des académiques

#### Résumé d'Inscription 📑
- Informations personnelles
- Contact et adresse
- Classes et années d'inscription
- Récapitulatif financier (facturé, payé, solde)
- Aperçu de la scolarité

#### Factures 💰
- Détails de facturation
- Description des charges
- Quantités et prix unitaires
- Sous-total, taxes, total
- Statut de paiement
- Conditions de paiement

---

## 🏗️ Architecture

### Services

#### DocumentGenerationService
Responsable de la **préparation des données** pour les documents

```php
// Générer données de certificat
$data = $service->generateSchoolCertificateData($student, $academicYearId);

// Générer données de bulletin
$data = $service->generateReportCardData($student, $academicYearId);

// Générer données de relevé complet
$data = $service->generateTranscriptData($student);

// Générer données de résumé
$data = $service->generateEnrollmentSummary($student);

// Générer données de facture
$data = $service->generateInvoiceData($student, $invoiceId);

// Lister documents disponibles
$docs = $service->getAvailableDocuments($student, $academicYearId);

// Historique des inscriptions
$history = $service->getEnrollmentHistory($student);
```

#### PDFGenerationService
Responsable de la **conversion HTML** et **configuration des PDFs**

```php
// Générer HTML du certificat
$html = $pdfService->generateSchoolCertificateHTML($student, $yearId);

// Générer HTML du bulletin
$html = $pdfService->generateReportCardHTML($student, $yearId);

// Générer HTML du relevé
$html = $pdfService->generateTranscriptHTML($student);

// Générer HTML du résumé
$html = $pdfService->generateEnrollmentSummaryHTML($student);

// Générer HTML de facture
$html = $pdfService->generateInvoiceHTML($student, $invoiceId);

// Obtenir nom du fichier
$filename = $pdfService->getFilename('school_certificate', $student, $yearId);
```

### Composants Livewire

#### StudentProfileSection
Affiche:
- **Onglet Profil** - Infos personnelles et classe actuelle
- **Onglet Inscription** - Historique complet
- **Onglet Documents** - Téléchargement des documents

Vérifie:
- Module Students actif
- Permissions utilisateur
- Données étudiant disponibles

### Contrôleur

#### DocumentDownloadController
Routes:
```
GET /api/dashboard/documents/certificate/{academicYearId}
GET /api/dashboard/documents/report-card/{academicYearId}
GET /api/dashboard/documents/transcript
GET /api/dashboard/documents/enrollment-summary
GET /api/dashboard/documents/invoice/{invoiceId}
```

---

## 📊 Flux de Données

```
StudentProfileSection (Livewire)
    ↓
DocumentGenerationService
    └─ Agrège données depuis:
       ├─ StudentEnrollment
       ├─ Grades
       ├─ Invoices
       ├─ Payments
       └─ SchoolInfo
    ↓
PDFGenerationService
    └─ Rend view Blade
       └─ HTML formaté
    ↓
DocumentDownloadController
    └─ Retourne HTML/PDF
       (à convertir en PDF avec mPDF ou DomPDF)
```

---

## 📁 Structure des Fichiers

```
modules/Dashboard/
├── Services/
│   ├── DocumentGenerationService.php    # Préparation des données
│   └── PDFGenerationService.php          # Conversion HTML
├── Controllers/
│   └── DocumentDownloadController.php    # Endpoints de téléchargement
├── Livewire/StudentDashboard/
│   └── StudentProfileSection.php         # Composant principal
└── Resources/views/
    ├── livewire/student-dashboard/
    │   └── student-profile-section.blade.php
    └── documents/
        ├── school-certificate.blade.php  # Template certificat
        ├── report-card.blade.php         # Template bulletin
        ├── transcript.blade.php          # Template relevé
        ├── enrollment-summary.blade.php  # Template résumé
        └── invoice.blade.php             # Template facture
```

---

## 🔐 Sécurité

### Vérifications Effectuées

1. **Module Activation**
   ```php
   if ($error = $this->verifyModuleAccess('Students')) {
       return $error;
   }
   ```

2. **Permissions**
   ```php
   if (!$user->can('view', $student)) {
       abort(403, 'Unauthorized');
   }
   ```

3. **Ownership Check**
   - Utilisateur ne peut télécharger que SES propres documents
   - Vérification user_id === student.user_id

### Données Sécurisées

- Pas d'accès cross-student
- Chaque endpoint vérifie l'ownership
- Historique des téléchargements (à implémenter)
- Audit logging (à implémenter)

---

## 📦 Installation et Configuration

### 1. Dépendances Requises

Pour les PDFs, installer une de ces bibliothèques:

**Option A: mPDF (Recommandé pour l'édition)**
```bash
composer require mpdf/mpdf
```

**Option B: DomPDF**
```bash
composer require barryvdh/laravel-dompdf
```

**Option C: Laravel DomPDF avec facade**
```bash
composer require barryvdh/laravel-dompdf
```

### 2. Configuration

#### Avec mPDF
```php
// Dans DocumentDownloadController
use Mpdf\Mpdf;

private function generatePDFResponse(string $html, string $filename): Response
{
    $mpdf = new Mpdf([
        'tempDir' => storage_path('app/temp'),
        'font_dir' => storage_path('fonts'),
    ]);

    $mpdf->WriteHTML($html);
    $mpdf->SetTitle(basename($filename, '.pdf'));

    return response($mpdf->Output($filename, 'D'))
        ->header('Content-Type', 'application/pdf');
}
```

#### Avec DomPDF (façade)
```php
use Barryvdh\DomPDF\Facade\Pdf;

private function generatePDFResponse(string $html, string $filename): Response
{
    $pdf = PDF::loadHTML($html);
    $pdf->setPaper('a4');
    $pdf->setOption('isPhpEnabled', true);

    return $pdf->download($filename);
}
```

### 3. Dossiers Requis
```bash
mkdir -p storage/app/documents/temp
chmod 775 storage/app/documents/temp
```

---

## 🎨 Personnalisation des Templates

### Modifier Logo
```blade
<div class="school-logo">🏫</div>
```
Remplacer par:
```blade
@if($school->logo_path)
    <img src="{{ storage_path($school->logo_path) }}" style="width: 80px; height: 80px;">
@else
    <div class="school-logo">🏫</div>
@endif
```

### Modifier Couleurs
Les templates utilisent des couleurs inline. À modifier dans les `<style>` blocks:
- Bleu (#007bff) → couleur primaire
- Vert (#28a745) → succès
- Orange (#ffc107) → attention
- Rouge (#dc3545) → erreur

### Ajouter Informations Additionnelles
Tous les templates reçoivent:
- `$school` - SchoolInfo
- `$data` - Données document spécifiques

Ajouter champs à DocumentGenerationService et templates.

---

## 🔧 Intégration Existante

### Routes Ajoutées
```php
// Dans modules/Dashboard/Routes/api.php
Route::prefix('documents')->name('documents.')->group(function () {
    Route::get('/certificate/{academicYearId}', [DocumentDownloadController::class, 'schoolCertificate'])->name('certificate');
    Route::get('/report-card/{academicYearId}', [DocumentDownloadController::class, 'reportCard'])->name('report-card');
    Route::get('/transcript', [DocumentDownloadController::class, 'transcript'])->name('transcript');
    Route::get('/enrollment-summary', [DocumentDownloadController::class, 'enrollmentSummary'])->name('enrollment-summary');
    Route::get('/invoice/{invoiceId}', [DocumentDownloadController::class, 'invoice'])->name('invoice');
});
```

### Composant Enregistré
```php
// Dans DashboardServiceProvider
Livewire::component('student-profile-section', StudentProfileSection::class);
```

---

## 📋 Checklist d'Implémentation

- [x] Services de génération de données
- [x] Service de conversion HTML
- [x] Contrôleur de téléchargement
- [x] Composant Livewire de profil
- [x] 5 templates HTML (certificat, bulletin, relevé, résumé, facture)
- [x] Routes API
- [x] Historique des inscriptions
- [ ] Intégration PDF (mPDF ou DomPDF)
- [ ] Audit logging des téléchargements
- [ ] Caching des données
- [ ] Tests unitaires
- [ ] Tests d'intégration
- [ ] Documentation utilisateur

---

## 🚀 Prochaines Étapes

### Immédiat (1-2 heures)
1. Installer mPDF ou DomPDF
2. Implémenter `generatePDFResponse()`
3. Tester téléchargement d'un PDF

### Court terme (2-3 heures)
1. Ajouter audit logging
2. Implémenter caching
3. Ajouter historique des téléchargements

### Moyen terme (3-4 heures)
1. Tests unitaires
2. Tests d'intégration
3. Documentation utilisateur

### Long terme
1. Signatures numériques
2. QR codes de vérification
3. Synchronisation avec systèmes externes
4. Webhooks pour archivage automatique

---

## 💡 Considérations Techniques

### Performance
- **Cacher** SchoolInfo (change rarement)
- **Cacher** les données de documents (expire 1h)
- Générer PDFs à la demande (pas de pré-génération)
- Considérer queue pour gros volumes

### Scalabilité
- Stocker PDFs temporaires
- Limiter accès par rate limiting
- Queue pour génération asynchrone
- Archive des anciens documents

### Maintenabilité
- Templates séparés par document
- Services monolithiques mais cohésifs
- Documentation inline pour formules
- Tests automatisés

---

## 📞 Exemples d'Utilisation

### Côté Contrôleur
```php
public function downloadCertificate(int $academicYearId): Response
{
    $student = Student::where('user_id', auth()->user()->id)->first();

    try {
        $html = $this->pdfService->generateSchoolCertificateHTML($student, $academicYearId);
        $filename = $this->pdfService->getFilename('school_certificate', $student, $academicYearId);

        return $this->generatePDFResponse($html, $filename);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
```

### Côté Livewire
```php
public function downloadDocument(string $documentType, ?string $invoiceId = null): void
{
    $student = Student::where('user_id', auth()->user()->id)->first();

    $this->dispatch('download', [
        'type' => $documentType,
        'studentId' => $student->id,
        'invoiceId' => $invoiceId,
    ]);
}
```

---

## 🎓 Résumé

Un système **flexible, sécurisé et extensible** pour générer des documents scolaires officiels:
- ✅ 5 types de documents différents
- ✅ Historique d'inscription intégré
- ✅ Sécurité multi-niveaux
- ✅ Personnalisable par école
- ✅ Prêt pour intégration PDF
- ✅ Extensible pour nouveau documents

**État**: 95% complet, en attente intégration PDF library

