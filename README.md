# Sistema de Gestão Académica

Aplicação web em **PHP puro** com **MySQL** para gestão académica completa: fichas de aluno, matrículas, planos de estudos, pautas e notas.

[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777BB4)](https://www.php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1)](https://www.mysql.com)

## 🚀 Quick Start

```bash
# 1. Clone/inicie o projeto
git clone <repo> gestao-academica
cd gestao-academica

# 2. BD
mysql -u root -p < database/schema.sql
php database/seed.php

# 3. Config (edite config/database.php e config/app.php)
# 4. Inicie Apache/XAMPP → http://localhost/gestao-academica/public/

# Test users:
# gestor@academia.pt / gestor123 (Gestor)
# func@academia.pt / func123 (Funcionário)
# aluno@academia.pt / aluno123 (Aluno)
```

---

## 📋 Requisitos

| Requisito | Versão Mínima |
|-----------|---------------|
| PHP | 8.1+ |
| MySQL | 8.0+/10.6+ |
| Apache | `mod_rewrite` |
| Extensões PHP | `pdo_mysql`, `fileinfo`, `mbstring` |

---

## 🛠️ Instalação Detalhada

### 1. Base de Dados
```bash
mysql -u root -p < database/schema.sql
php database/seed.php  # Cria users de teste
```

### 2. Configuração
**`config/database.php`:**
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'academic_system');  # Ajuste
define('DB_USER', 'root');
define('DB_PASS', '');
```

**`config/app.php`:**
```php
define('APP_URL', 'http://localhost/gestao-academica/public');
```

### 3. Permissões
```bash
chmod -R 755 public/uploads/
```

---

## 🎯 Funcionalidades por Perfil

### 👨‍💼 **Gestor**
- Dashboard com overview
- Gerir **Cursos** (criar/editar/toggle ativo)
- Gerir **UCs** (criar/editar/toggle, associar a curso)
- **Planos de Estudos** (ver/remover)
- **Fichas de Alunos** (listar/validar rascunhos)

### 👨‍💼 **Funcionário**
- Dashboard
- Gerir **Matrículas** (listar/decidir aprovar/rejeitar)
- Gerir **Pautas** (criar/fechar, lançar notas)

### 👨‍🎓 **Aluno**
- Dashboard pessoal
- **Ficha** (submeter rascunho)
- **Matrículas** (ver/listar, ver cursos/UCs disponíveis)
- **Notas** (ver por curso/pauta)

### 🔐 **Autenticação**
- Login/registo
- Restrição por perfil
- Logout seguro

---

## 📁 Estrutura do Projeto (Atualizada)

```
gestao-academica/
├── .gitignore
├── .htaccess
├── README.md
├── TODO.md
├── config/
│   ├── app.php
│   └── bootstrap.php  # + database.php
├── database/
│   ├── schema.sql
│   └── seed.php
├── public/  # 📂 Apenas este exposto ao web
│   ├── .htaccess
│   ├── index.php
│   ├── login.php, logout.php, register.php
│   ├── aluno/  👨‍🎓
│   │   ├── dashboard.php
│   │   ├── ficha.php
│   │   ├── matricula-nova.php
│   │   ├── matriculas.php
│   │   └── notas-curso.php
│   ├── funcionario/  👨‍💼
│   │   ├── dashboard.php
│   │   ├── matricula-decidir.php
│   │   ├── matriculas.php
│   │   ├── pauta-nova.php
│   │   ├── pauta-notas.php
│   │   └── pautas.php
│   ├── gestor/  👨‍💼
│   │   ├── curso-editar.php, curso-novo.php, curso-toggle.php
│   │   ├── cursos.php
│   │   ├── dashboard.php
│   │   ├── ficha-validar.php
│   │   ├── fichas.php
│   │   ├── plano-estudos.php, plano-remover.php
│   │   ├── uc-editar.php, uc-nova.php, uc-toggle.php
│   │   └── ucs.php
│   ├── css/style.css
│   └── uploads/photos/  # Fotos alunos
├── src/
│   ├── Controllers/AuthController.php
│   ├── Middleware/ (UploadHelper.php, Validator.php)
│   └── Models/  # Um por entidade
│       ├── CursoModel.php
│       ├── FichaAlunoModel.php
│       ├── MatriculaModel.php
│       ├── PautaModel.php
│       ├── PlanoEstudosModel.php
│       ├── UCModel.php
│       └── UtilizadorModel.php
└── views/  # Templates (não direct access)
    ├── layouts/main.php
    ├── auth/ (login.php, register.php)
    ├── aluno/*  👨‍🎓
    ├── funcionario/*  👨‍💼
    ├── gestor/*  👨‍💼 (inclui fichas.php, uc-form.php)
    └── shared/403.php
```

---

## 🔄 Fluxos Principais

1. **Ficha Aluno**: Rascunho → Submetida → **Gestor Aprova/Rejeita**
2. **Matrícula**: Aluno vê **Cursos + UCs** → Pede → Funcionário **Aprova/Rejeita**
3. **Pauta**: Funcionário cria (aberta) → Lança notas → **Fecha**
4. **Novidade**: `/aluno/notas-curso.php` - Notas detalhadas por curso

---

## 🛡️ Segurança & Best Practices

- ✅ Passwords bcrypt (`password_hash`)
- ✅ Sessões seguras (regen ID, timeout)
- ✅ PDO prepared statements (anti-SQLi)
- ✅ Uploads validados (`finfo` MIME)
- ✅ XSS: `htmlspecialchars()` everywhere
- ✅ CSRF? Via session checks + referer
- ✅ .htaccess: security headers, deny hidden files

**Arquitetura**: MVC lightweight - cada `public/*.php` → bootstrap → auth check → model → view.



