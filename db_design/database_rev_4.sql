create table cor_accounts
(
  reg_id        bigint auto_increment comment 'It will be used on detailed transactions.'
    primary key,
  userid        varchar(50)                          not null,
  cost_value    int(10)                              not null comment 'Montant',
  prev_num_days int        default 0                 not null comment 'Jours restants sur l''ancien abonnement lors dune prorogation',
  num_days      int(2)                               not null comment 'Nombre de jour de l''abonnement.',
  total_days    int as ((`prev_num_days` + `num_days`)) stored comment 'Nombre total de jours de l abonnement. prev_num_days+num_days',
  created_at    timestamp  default CURRENT_TIMESTAMP not null,
  ended_at      timestamp as ((`created_at` + interval `total_days` day)) stored,
  reg_status    tinyint(1) default 1                 not null comment 'Statut actif/non-actif de l''abonnement',
  user_deleted  tinyint(1) default 0                 not null comment 'L''utilisateur peut choisir de supprimer l''historique de ses abonnements, mais nous gardons la trace.'
)
  comment 'Abonnements des utilisateurs. une routine-trigger sera mise en place pour le calcul entre la date courante et celle de date_reg, faire une comparaison avec number_days et changer le status de reg_status a true ou false.'
  engine = InnoDB;

create table cor_balances
(
  balance_id bigint auto_increment
    primary key,
  userid     varchar(45)   not null,
  deposit    int default 0 not null,
  credit     int default 0 not null,
  debit      int default 0 not null,
  retirement int default 0 not null
)
  comment 'Balances des comptes en deposit, credit, debit et retirement';

create table cor_emissions
(
  emission_id    bigint auto_increment
    primary key,
  type_operation enum ('deposit', 'payment', 'retirement') not null,
  funds_origin   varchar(30)                               null comment 'OM, MoMo, Cash, virement',
  user_from      varchar(45)                               not null,
  user_to        varchar(45)                               not null,
  amount         int                                       not null,
  description    varchar(255)                              null,
  created_at     timestamp default CURRENT_TIMESTAMP       not null
)
  comment 'Transactions utilisateur';

create definer = root@localhost trigger newBalance
  after INSERT
  on cor_emissions
  for each row
BEGIN
  IF NEW.type_operation = 'deposit'
  THEN
    INSERT INTO cor_balances(userid, deposit) VALUES (NEW.user_from, NEW.amount);
  END IF;
  IF NEW.type_operation = 'retirement'
  THEN
    INSERT INTO cor_balances(userid, retirement) VALUES (NEW.user_from, NEW.amount);
  END IF;
  IF NEW.type_operation = 'payment'
  THEN
    INSERT INTO cor_balances(userid, debit) VALUES (NEW.user_from, NEW.amount);
    INSERT INTO cor_balances(userid, credit) VALUES (NEW.user_to, NEW.amount);
  END IF;
END;

create table cor_users
(
  userid          varchar(45)                          not null comment 'md5(replace(UUID(), ''-'', ''''))',
  username        varchar(16)                          null,
  email           varchar(150)                         null,
  phone_number    varchar(15)                          not null,
  password        varchar(255)                         not null,
  token           varchar(45)                          null,
  pincode         char(8)    default '12345678'        not null,
  urgency_pincode varchar(8)                           null,
  firstname       varchar(45)                          not null,
  lastname        varchar(45)                          not null,
  birth_date      date                                 null,
  gender          enum ('female', 'male', 'company')   not null,
  address         varchar(150)                         null,
  id_card         char(15)                             null,
  picture         varchar(255)                         null comment 'Image de profil',
  created_at      timestamp  default CURRENT_TIMESTAMP not null,
  updated_at      timestamp  default CURRENT_TIMESTAMP not null on update CURRENT_TIMESTAMP,
  is_activated    tinyint(1) default 0                 not null,
  constraint email_UNIQUE
    unique (email),
  constraint phone_number_UNIQUE
    unique (phone_number),
  constraint userid_UNIQUE
    unique (userid),
  constraint username_UNIQUE
    unique (username)
)
  engine = InnoDB;

alter table cor_users
  add primary key (userid);


