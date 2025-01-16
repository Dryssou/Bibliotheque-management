# 📚 Bibliothèque Management
Un système moderne de gestion de bibliothèque développé avec Symfony 6, permettant la gestion complète des livres, auteurs, et interactions utilisateurs.

## ✨ Fonctionnalités

- 🔐 Système d'authentification complet avec récupération de mot de passe
- 👥 Gestion des utilisateurs avec rôles (Admin, User)
- 📚 Gestion des livres et des auteurs
- 💬 Système de discussions et commentaires
- 🎨 Interface responsive avec Tailwind CSS
- 👑 Panel d'administration complet

## 🛠️ Technologies Utilisées

- PHP 8.2
- Symfony 6.4
- MySQL/MariaDB
- Tailwind CSS 3
- Font Awesome 6
- Docker (optionnel)

## 🚀 Installation

1. Clonez le repository
```bash
git clone https://github.com/votre-username/Bibliotheque-management.git
cd Bibliotheque-management
```

2. Installez les dépendances
```bash
composer install
npm install
```

3. Configurez votre environnement
```bash
# Copiez le fichier d'environnement
cp .env .env.local

# Configurez votre base de données dans .env.local
DATABASE_URL="mysql://user:password@127.0.0.1:3306/bibliotheque?serverVersion=8.0"
```

4. Créez la base de données
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

5. Chargez les fixtures (données de test)
```bash
php bin/console doctrine:fixtures:load
```

6. Compilez les assets
```bash
npm run build
```

7. Lancez le serveur
```bash
symfony serve -d
```

## 👤 Comptes par défaut

- Admin: admin@example.com / admin123
- User: user@example.com / user123

## 📱 Pages principales

- `/` - Accueil
- `/login` - Connexion
- `/register` - Inscription
- `/books` - Catalogue des livres
- `/authors` - Liste des auteurs
- `/discussions` - Forum de discussions
- `/admin` - Panel d'administration

## 🧪 Tests

```bash
# Lancer les tests unitaires
php bin/phpunit

# Lancer les tests avec couverture
php bin/phpunit --coverage-html coverage
```

## 📝 Contribution

1. Fork le projet
2. Créez votre branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push sur la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📄 License

Ce projet est sous licence MIT - voir le fichier [LICENSE.md](LICENSE.md) pour plus de détails

## 🙏 Remerciements

- [Symfony](https://symfony.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Font Awesome](https://fontawesome.com/)

## 👨‍💻 Auteur

Dryssou
