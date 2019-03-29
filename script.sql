create table usf_accounts
(
  id           int auto_increment
    primary key,
  name         varchar(100)      not null,
  description  varchar(255)      not null,
  account_type int     default 1 not null,
  status       tinyint default 1 not null,
  constraint usf_accounts_name_uindex
    unique (name)
);

create table usf_languages
(
  id          int auto_increment
    primary key,
  code2       varchar(2)        not null,
  code3       varchar(3)        null,
  name        varchar(100)      not null,
  native_name varchar(100)      null,
  is_active   tinyint default 0 not null,
  constraint usf_languages_code2_uindex
    unique (code2),
  constraint usf_languages_code3_uindex
    unique (code3),
  constraint usf_languages_name_uindex
    unique (name)
);

create table usf_sessions
(
  id         int auto_increment
    primary key,
  token      varchar(32)                         not null,
  useragent  varchar(256)                        null,
  ip         varchar(15)                         null,
  start_time timestamp default CURRENT_TIMESTAMP not null,
  end_time   timestamp default CURRENT_TIMESTAMP not null,
  data       text                                null,
  user       int       default 0                 null,
  active     tinyint   default 1                 not null,
  constraint usf_sessions_token_uindex
    unique (token)
);

create table usf_users
(
  id        int auto_increment
    primary key,
  name      varchar(100)      not null,
  user_type int     default 1 not null,
  status    tinyint default 1 not null,
  constraint usf_users_name_uindex
    unique (name)
);

