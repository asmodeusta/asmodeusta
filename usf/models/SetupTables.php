<?php

namespace Usf\Models;


class SetupTables
{

    public function exec()
    {
        $sql = "
        create table usf_languages
(
	id int auto_increment,
	code2 varchar(2) not null,
	code3 varchar(3) null,
	name varchar(100) not null,
	native_name varchar(100) null,
	active tinyint default 0 not null,
	constraint usf_languages_pk
		primary key (id)
);

create unique index usf_languages_code2_uindex
	on usf_languages (code2);

create unique index usf_languages_code3_uindex
	on usf_languages (code3);

create unique index usf_languages_name_uindex
	on usf_languages (name);


        ";
    }

}