USE module_b;

CREATE TABLE IF NOT EXISTS users (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(50)  NOT NULL,
  email      VARCHAR(100) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  phone      VARCHAR(20)  NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS concerts (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  title       VARCHAR(100) NOT NULL,
  description TEXT,
  venue       VARCHAR(100) NOT NULL,
  date        DATETIME     NOT NULL,
  price       INT          NOT NULL DEFAULT 5000,
  total_seats INT          NOT NULL,
  poster      VARCHAR(100),
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS bookings (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  user_id     INT  NOT NULL,
  concert_id  INT  NOT NULL,
  total_price INT  NOT NULL,
  booked_at   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)    REFERENCES users(id),
  FOREIGN KEY (concert_id) REFERENCES concerts(id)
);

CREATE TABLE IF NOT EXISTS booking_seats (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT         NOT NULL,
  concert_id INT         NOT NULL,
  seat       VARCHAR(10) NOT NULL,
  UNIQUE KEY uq_seat (concert_id, seat),
  FOREIGN KEY (booking_id) REFERENCES bookings(id),
  FOREIGN KEY (concert_id) REFERENCES concerts(id)
);

INSERT INTO concerts (title, description, venue, date, price, total_seats, poster) VALUES
('뮤지컬 별빛 속으로', '잃어버린 기억을 찾아 떠나는 남자의 여정을 그린 창작 뮤지컬', '서울 블루아트홀',   '2026-05-03 19:00:00', 5000, 40, 'poster_01.png'),
('하늘소 전국 투어',   '데뷔 10주년 전국 투어 콘서트',                                '부산 드림아트센터', '2026-05-08 18:00:00', 5000, 50, 'poster_02.png'),
('뮤지컬 달의 궁전',   '조선시대 배경 창작 뮤지컬',                                   '서울 그랜드씨어터', '2026-05-12 19:30:00', 5000, 30, 'poster_03.png'),
('강바람 단독 공연',   '감성 싱어송라이터 강바람의 두 번째 단독 공연',                 '대구 스타디움홀',   '2026-05-17 17:00:00', 5000, 60, 'poster_04.png'),
('뮤지컬 붉은 노을',   '1970년대 배경 창작 뮤지컬',                                   '인천 아트센터',     '2026-05-22 19:00:00', 5000, 40, 'poster_05.png'),
('달빛소년단 콘서트',  '4인조 혼성 밴드 달빛소년단의 첫 단독 콘서트',                 '광주 빛고을아트홀', '2026-05-28 20:00:00', 5000, 30, 'poster_06.png'),
('뮤지컬 푸른 항구',   '항구 도시를 배경으로 펼쳐지는 유쾌한 창작 뮤지컬',           '대전 예술의전당',   '2026-06-03 19:00:00', 5000, 50, 'poster_07.png'),
('서린 콘서트 ECHO',   '솔로 가수 서린의 세 번째 단독 콘서트',                        '서울 올림픽홀',     '2026-06-10 18:30:00', 5000, 40, 'poster_08.png');
