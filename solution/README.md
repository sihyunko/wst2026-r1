# 실행 방법

XAMPP 또는 Docker로 실행할 수 있습니다.

---

## XAMPP로 실행하기

### 1. htdocs에 파일 복사

XAMPP의 `htdocs` 폴더 안에 프로젝트 파일을 복사합니다.

### 2. DB 생성 및 테이블 초기화

phpMyAdmin(`http://localhost/phpmyadmin`)에서 `module_b` 데이터베이스를 생성한 후, `initdb/init.sql` 파일을 import합니다.

### 3. DB 접속 정보 수정

`htdocs/module_b/config/database.php` 파일에서 `host`를 아래와 같이 변경합니다.

```php
'host' => 'localhost',
```

### 4. 접속 주소

```txt
http://localhost
```

---

## Docker로 실행하기

XAMPP와 비슷한 환경의 Docker로 실행 합니다.

### 1. Docker Desktop 실행

Docker Desktop을 실행합니다.

실행 후 왼쪽 아래 또는 화면 하단에 Docker가 실행 중인지 확인합니다.

### 2. 프로젝트 폴더 열기

터미널 또는 PowerShell에서 이 프로젝트 폴더로 이동합니다.

### 3. Docker 실행

아래 명령어를 입력합니다.

```bash
docker compose up -d --build
```

처음 실행할 때는 시간이 조금 걸릴 수 있습니다.

### 4. 접속 주소

웹사이트:

```txt
http://localhost
```

phpMyAdmin:

```txt
http://localhost/phpmyadmin
```

---

### 5. phpMyAdmin 로그인 정보

```txt
사용자명: root
암호: 비워두기
```

---

### 6. MySQL 접속 정보

PHP에서 MySQL 접속 시 아래 정보를 사용합니다.

```txt
호스트: db
포트: 3306
DB명: module_b
사용자명: root
비밀번호: 없음
```

예시:

```php
<?php

$conn = mysqli_connect("db", "root", "", "module_b");

if (!$conn) {
    die("DB 연결 실패: " . mysqli_connect_error());
}

echo "DB 연결 성공";
```

---

### 7. Docker 종료

아래 명령어를 입력합니다.

```bash
docker compose down
```