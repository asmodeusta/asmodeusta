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
  id          smallint(6) auto_increment
    primary key,
  code2       varchar(2)   not null,
  code3       varchar(3)   null,
  name        varchar(100) not null,
  native_name varchar(100) null,
  is_active   tinyint      null comment '0',
  constraint languages_code2_uindex
    unique (code2),
  constraint languages_code3_uindex
    unique (code3),
  constraint languages_name_uindex
    unique (name)
);

create table usf_modules
(
  id          int auto_increment
    primary key,
  slag        varchar(100)                not null,
  name        varchar(255)                not null,
  description text                        not null,
  path        varchar(255)                not null,
  file        varchar(255)                not null,
  version     varchar(31) default '1.0.0' not null,
  active      tinyint(1)  default 0       not null,
  constraint usf_modules_slag_uindex
    unique (slag)
);

create table usf_options
(
  id       int auto_increment
    primary key,
  module   int        default 0                 not null,
  `key`    varchar(100)                         not null,
  value    text                                 null,
  modified timestamp  default CURRENT_TIMESTAMP not null,
  autoload tinyint(1) default 0                 not null,
  autosave tinyint(1) default 1                 not null,
  active   tinyint(1) default 1                 not null
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
  active     tinyint   default 1                 null,
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

