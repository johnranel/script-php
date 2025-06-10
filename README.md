# 1. PHP Script Database Importer

Command-line PHP script to manage importing of user data from a parsed .CSV file.

## Features

- Create a new MySQL database and `users` table
- Import user data from a .CSV file
- Format and validate the name, surname, and email columns
- Skip any invalid or duplicate email entries
---
## Requirements

- `PHP8.1` or higher
- `MySQL 8.x` or `MariaDB 11.x`
- PHP Extension:
    - `mysqli` required

### To install required extensions on Ubuntu:
```
apt update
apt install php8.1-mysqli
```

## Usage

Using the command-line run the script using:
```
php user_upload.php [OPTIONS]
```

### Available Options

| Option                        | Description   |
| -------------                 |---------------|
| `--file [csv file name]`      | this will take the name of the CSV to be parsed and inserted to the database     |
| `--create_table`              | this will cause the MySQL users table to be built (and no further action will be taken)     |
| `--dry_run`                   | this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the database won't be altered     |
| `-u`                   | MySQL username     |
| `-p`                   | MySQL password     |
| `-h`                   | MySQL host (e.g. localhost, 127.0.0.1, or Docker IP)     |
| `--help`                      | this will output the above list of directives with details.|

### Required MySQL Options

- `-u` MySQL username
- `-p` MySQL password
- `-h` MySQL host (e.g. localhost, 127.0.0.1, or Docker IP)

## Examples

1. Show help menu
```
php user_upload.php --help
```
2. Create Database and Users table
```
php user_upload.php --create_table -u root -p secret -h localhost
```
3. Dry run import (No database insert)
```
php user_upload.php --file users.csv -u root -p secret -h localhost --dry_run
```
4. Run import (Database insert)
```
php user_upload.php --file users.csv -u root -p secret -h localhost
```

## CSV Format

Your .CSV file should have the following header names:
```
name,surname,email
John,Doe,johndoe@example.com
```

## Data Validation
- **Name & Surname:** Trimmed, capitalized, and removed numbers and special characters.
- **Email:** Trimmed, Lowercased, and format must be valid (e.g. `johndoe@example.com`)
- **Duplicates:** Skipped during database insert
- **Contains invalid email:** Skipped during database insert

## Output

Sample output during database insert.

```
name: John surname: Doe email: johndoe@example.com
Inserted
Valid

name: Jane surname: Smith email: janesmith@examp@le.com
Contains invalid data - SKIPPED
Invalid

name: John surname: Doe email: johndoe@example.com
Duplicate entry - SKIPPED
Valid
```

## Notes

- The script creates and uses a database named `catalyst` by default using the command `--create_table`.
- Table name is fixed as `users`.
- Sanitization and email validation are applied before insertion.

# 2. PHP Foobar

Command-line PHP script that iterates through 1-100 and return foobar if current number is divisible by 3 and 5, foo if divisible by 3, bar if divisible by 5 and return the current number if not divisible by either 3 and 5.

## Usage

Using the command-line run the script using:
```
php foobar.php
```

## Output

Sample output after running the script.

```
1, 2, foo, 4, bar, foo, 7, 8, foo, bar, 11, foo, 13, 14....
```