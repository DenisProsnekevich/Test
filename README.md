# RichBrains Test Project

Welcome to the **RichBrains Test Project** repository!

## Table of Contents

1. [Getting Started](#getting-started)
2. [Prerequisites](#prerequisites)
3. [Usage](#usage)

## Getting Started

These instructions will guide you through setting up and running the project on your local machine.

### Prerequisites

Before you begin, ensure you have the following installed:

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Usage

To run the project, navigate to the project directory and execute the following command:

```bash
docker compose up
```

After the project has started, open your web browser and go to:

http://localhost:8080/

This will allow you to view the application.




# Улучшения каталога книг

## Task 1: Добавление функционала категорий

### Реализация системы категорий

- Реализована система категорий и подкатегорий.
- Каждой книге можно назначить или убрать категорию через `select` с `placeholder`.

### Ограничения на удаление категорий

1. Нельзя удалить категорию, если она связана хотя бы с одной книгой.
2. Нельзя удалить категорию, если у неё есть дочерние категории.
3. В обоих случаях выводится предупреждение при попытке удаления.

### Защита от циклических связей

1. Использован кастомный валидатор `NoCircularReference` на уровне сущности (для него написан тест).
2. Предотвращает ситуации вида `C → B → A → C`, где категория становится своим потомком.

### Бизнес-логика в `CategoryService.php`

1. Сервис управляет бизнес-логикой категорий книг.
2. Работает поверх репозитория.
3. Отвечает за построение дерева категорий, их отображение, безопасное удаление, исключая указанную и её потомков (например, чтобы предотвратить циклические связи при выборе родителя).
4. Внутри сервиса вызывается метод репозитория, который возвращает категории в виде иерархического массива.
5. Также реализован метод для «красивого» отображения категорий с отступами.

---

## Task 2: Исправление проблемы с сохранением описания

### Выявленные проблемы

- Поле `description` было ограничено 80 символами на уровне сущности.
- Использовался `FormEvents::PRE_SUBMIT`, который обрезал описание до 80 символов.

### Принятые решения

1. Тип поля `description` изменён на `TEXT` в базе данных.
2. Установлено ограничение длины на уровне сущности (`Assert\Length`).
3. Удалён функционал, обрезающий описание перед отправкой формы.
4. Добавлено ограничение `maxlength` в форме и сущности. Если валидация не проходит — выводится предупреждение.
5. В шаблоне (`Twig`) добавлено предупреждение о максимально допустимой длине.






# Book Catalog Enhancements

## Task 1: Add Category Feature to Books

### Category System Implementation

- Implemented a hierarchical category system with support for subcategories.
- Each book can be assigned or unassigned a category using a `select` input with a placeholder.

### Deletion Restrictions

1. A category cannot be deleted if it is associated with any books.
2. A category cannot be deleted if it has child categories.
3. In both cases, a warning message is displayed when deletion is attempted.

### Circular Reference Protection

1. A custom validator `NoCircularReference` is used at the entity level (a test has been written for it).
2. Prevents circular relationships such as `C → B → A → C`, where a category becomes its own descendant.

### Business Logic in `CategoryService.php`

1. Manages all business logic related to book categories.
2. Operates on top of the repository layer.
3. Responsible for building the category tree, rendering categories, and safely deleting categories while excluding the selected category and its descendants (to prevent circular references when choosing a parent).
4. Internally calls repository methods that return categories as a hierarchical array.
5. Includes a method for formatted category output with indentation for nested levels.

---

## Task 2: Fix the Description Saving Problem

### Issue Identified

- The `description` field was limited to 80 characters at the entity level.
- A `FormEvents::PRE_SUBMIT` listener was trimming the description to 80 characters before saving.

### Solutions Applied

1. Changed the `description` field type to `TEXT` in the database.
2. Added a length constraint at the entity level using `Assert\Length`.
3. Removed the trimming logic from the form submission process.
4. Added a `maxlength` attribute in both the form and entity validation. If validation fails, a warning message is displayed.
5. Included a warning in the Twig template to inform users of the maximum allowed length.
