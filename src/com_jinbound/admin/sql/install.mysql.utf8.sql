################################################################################
##  JInbound SQL Install                                                      ##
################################################################################;


########################################
##    Campaigns                       ##
##                                    ##
##    Campaigns are basically just    ##
##    simple tags to categorize the   ##
##    emails                          ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_campaigns` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `label`            TINYINT(1)                DEFAULT '0'
  COMMENT 'label type',

  `greedy`           TINYINT(1)                DEFAULT '0'
  COMMENT 'this campaign wants the contacts to itself',

  `conversion_url`   TEXT             NOT NULL DEFAULT ''
  COMMENT 'query params that denote a successful conversion',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  `params`           TEXT
  COMMENT 'JSON encoded custom parameters',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Emails                          ##
##                                    ##
##    Emails to be sent to the user   ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_emails` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `type`             VARCHAR(255)     NOT NULL DEFAULT 'campaign'
  COMMENT 'type of record',

  `campaign_id`      INT(11)          NOT NULL
  COMMENT 'Primary key of associated campaign',

  `layout`           VARCHAR(1)       NOT NULL DEFAULT "A"
  COMMENT 'Layout type - 0 for custom, or A-D',

  `fromname`         VARCHAR(255)     NOT NULL,
  `fromemail`        VARCHAR(255)     NOT NULL,
  `sendafter`        INT(4)           NOT NULL,
  `subject`          VARCHAR(255)     NOT NULL,
  `htmlbody`         BLOB             NOT NULL,
  `plainbody`        BLOB             NOT NULL,
  `params`           BLOB             NOT NULL,

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Email Records                   ##
##                                    ##
##    Records of emails already sent  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_emails_records` (

  `id`         INT(11)  NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`   INT(11)  NOT NULL
  COMMENT 'Key for assets table',

  `email_id`   INT(11)  NOT NULL
  COMMENT 'Primary Key of associated Email',

  `version_id` INT(11)  NOT NULL DEFAULT 0
  COMMENT 'Primary Key of associated Email version',

  `lead_id`    INT(11)  NOT NULL
  COMMENT 'Primary key of associated Lead',

  `sent`       DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Leads                           ##
##                                    ##
##    Leads are associated with       ##
##    contacts, which will be created ##
##    when saving a new lead or       ##
##    updated when this lead is       ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_leads` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `page_id`          INT(11)          NOT NULL
  COMMENT 'Primary key of Page associated with this lead',

  `contact_id`       INT(11)          NOT NULL
  COMMENT 'Primary key of Contact associated with this lead',

  `priority_id`      INT(11)          NOT NULL
  COMMENT 'Primary key of Priority associated with this lead',

  `status_id`        INT(11)          NOT NULL
  COMMENT 'Primary key of Status associated with this lead',

  `campaign_id`      INT(11)          NOT NULL
  COMMENT 'Primary key of Campaign associated with this lead',

  `first_name`       VARCHAR(255)     NOT NULL
  COMMENT 'First name of Lead (contacts uses single column for both names)',

  `last_name`        VARCHAR(255)     NOT NULL
  COMMENT 'Last name of Lead (contacts uses single column for both names)',

  `ip`               VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'IPv4/6 address of record creator',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  `formdata`         TEXT
  COMMENT 'JSON encoded form data',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Lead Statuses                   ##
##                                    ##
##    Leads are associated with       ##
##    contacts, which will be created ##
##    when saving a new lead or       ##
##    updated when this lead is       ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_lead_statuses` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `description`      MEDIUMTEXT       NOT NULL
  COMMENT 'description of this record',

  `ordering`         INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'Ordering column for priority level',

  `default`          TINYINT(1)                DEFAULT '0'
  COMMENT 'Default status',

  `active`           TINYINT(1)                DEFAULT '0'
  COMMENT 'Active statuses count towards leads, inactive do not',

  `final`            TINYINT(1)                DEFAULT '0'
  COMMENT 'Final status',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Pages                           ##
##                                    ##
##    Landing pages users will visit  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_pages` (

  `id`                        INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`                  INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `layout`                    VARCHAR(1)       NOT NULL DEFAULT "A"
  COMMENT 'Layout type - 0 for custom, or A-D',

  `heading`                   VARCHAR(255)     NOT NULL,
  `subheading`                VARCHAR(255)     NOT NULL,
  `socialmedia`               TINYINT(1)       NOT NULL,
  `maintext`                  BLOB             NOT NULL,
  `sidebartext`               BLOB             NOT NULL,
  `alias`                     VARCHAR(255)     NOT NULL,
  `name`                      VARCHAR(255)     NOT NULL,
  `image`                     VARCHAR(255)     NOT NULL,
  `imagealttext`              VARCHAR(255)     NOT NULL,

  `image_size_large_width`    VARCHAR(50)      NOT NULL DEFAULT '',
  `image_size_large_height`   VARCHAR(50)      NOT NULL DEFAULT '',
  `image_size_medium_width`   VARCHAR(50)      NOT NULL DEFAULT '',
  `image_size_medium_height`  VARCHAR(50)      NOT NULL DEFAULT '',
  `image_size_small_width`    VARCHAR(50)      NOT NULL DEFAULT '',
  `image_size_small_height`   VARCHAR(50)      NOT NULL DEFAULT '',

  `category`                  VARCHAR(255)     NOT NULL,
  `metatitle`                 VARCHAR(55)      NOT NULL,
  `metadescription`           VARCHAR(155)     NOT NULL,
  `formid`                    INT(11)          NOT NULL DEFAULT 0,
  `formname`                  VARCHAR(255)     NOT NULL,
  `formbuilder`               BLOB             NOT NULL,
  `campaign`                  INT(11)          NOT NULL,
  `converts_on_another_form`  VARCHAR(25)      NOT NULL,
  `converts_on_same_campaign` VARCHAR(25)      NOT NULL,
  `submit_text`               VARCHAR(255)     NOT NULL,
  `notify_form_submits`       VARCHAR(255)     NOT NULL,
  `notification_email`        VARCHAR(255)     NOT NULL,
  `after_submit_sendto`       VARCHAR(20)      NOT NULL,
  `menu_item`                 VARCHAR(10)      NOT NULL,
  `send_to_url`               VARCHAR(255)     NOT NULL,
  `sendto_message`            TEXT             NOT NULL,
  `template`                  TEXT             NOT NULL,
  `css`                       TEXT             NOT NULL DEFAULT '',

  `ga`                        TINYINT(1)       NOT NULL DEFAULT 0,
  `ga_code`                   VARCHAR(255)     NOT NULL DEFAULT '',

  `hits`                      INT(11)                   DEFAULT '0'
  COMMENT 'number of views for this record',

  `published`                 TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`                   DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`                INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`                  DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`               INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`               INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Priorities                      ##
##                                    ##
##    Allow leads to be sorted by     ##
##    custom priority                 ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_priorities` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `description`      MEDIUMTEXT       NOT NULL
  COMMENT 'description of this record',

  `ordering`         INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'Ordering column for priority level',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Stages                          ##
##                                    ##
##    Customizeable steps to show     ##
##    where leads are in the process  ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_stages` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `name`             VARCHAR(255)     NOT NULL
  COMMENT 'name of this record',

  `description`      MEDIUMTEXT       NOT NULL
  COMMENT 'description of this record',

  `ordering`         INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'Ordering column for stage level',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Notes                           ##
##                                    ##
##    Lead notes                      ##
########################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_notes` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'Key for assets table',

  `lead_id`          INT(11)          NOT NULL
  COMMENT 'Key for leads table',

  `text`             MEDIUMTEXT       NOT NULL
  COMMENT 'note text',

  `published`        TINYINT(1)                DEFAULT '1'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

########################################
##    Subscriptions                   ##
##                                    ##
##    Simple xref table to track      ##
##    if leads should be sent emails  ##
########################################;

CREATE TABLE IF NOT EXISTS #__jinbound_subscriptions (

  `id` INT (11
) NOT NULL AUTO_INCREMENT
COMMENT 'Primary Key',

  `contact_id` INT (11
) NOT NULL
COMMENT 'FK to #__contact_details.id',

  `enabled` INT (1
) NOT NULL DEFAULT 1
COMMENT 'If 0 emails will not be sent',

PRIMARY KEY (`id`
)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

########################################
##    Email Versions                  ##
##                                    ##
##    stores copies of previous       ##
##    versions of emails for accurate ##
##    recordkeeping                   ##
########################################;

CREATE TABLE IF NOT EXISTS #__jinbound_emails_versions (

  `id` INT (11
) NOT NULL AUTO_INCREMENT
COMMENT 'Primary Key',

  `email_id` INT (11
) NOT NULL
COMMENT 'FK to #__jinbound_emails.id',

  `subject` VARCHAR (255
),
  `htmlbody` BLOB,
  `plainbody` BLOB,

PRIMARY KEY (`id`
)
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

## updated database structure ;

#####################################################
## Contacts, replaces core integration and contains entries for guests based on tracking
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `user_id`          INT(11)          NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `core_contact_id`  INT(11)          NOT NULL DEFAULT 0
  COMMENT 'FK to #__contact_details table',

  `asset_id`         INT(11)          NOT NULL
  COMMENT 'FK to #__assets table',

  `cookie`           VARCHAR(255)
  COMMENT 'Cookie value',

  `alias`            VARCHAR(255)     NOT NULL
  COMMENT 'Slug for URL',

  `first_name`       VARCHAR(255)     NOT NULL
  COMMENT 'First name of Contact',

  `last_name`        VARCHAR(255)     NOT NULL
  COMMENT 'Last name of Contact',

  `company`          VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Company name of Contact',

  `website`          VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Company name of Contact',

  `email`            VARCHAR(255)     NOT NULL
  COMMENT 'Email of Contact',

  `address`          TEXT             NOT NULL DEFAULT ''
  COMMENT 'Address of Contact',

  `suburb`           VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'City/suburb of Contact',

  `state`            VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'State of Contact',

  `country`          VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'Country of Contact',

  `postcode`         VARCHAR(100)     NOT NULL DEFAULT ''
  COMMENT 'Postal code of Contact',

  `telephone`        VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Telephone number of Contact',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Associates tracks to contacts
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_users_tracks` (

  `user_id` INT(11) NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `cookie`  VARCHAR(255)
  COMMENT 'Cookie value'

)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

#####################################################
## Tracks, details about where a user has been
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_tracks` (

  `id`               VARCHAR(128) NOT NULL
  COMMENT 'Primary Key, microtime + ip + md5(session id)',

  `cookie`           VARCHAR(255)
  COMMENT 'Cookie value for this track',

  `detected_user_id` INT(11)      NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `current_user_id`  INT(11)      NOT NULL DEFAULT 0
  COMMENT 'FK to #__users table',

  `user_agent`       TEXT
  COMMENT 'Browser User Agent',

  `created`          DATETIME     NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date this request was made',

  `ip`               VARCHAR(64)
  COMMENT 'IP address as determined by PHP, either ipv4 or ipv6',

  `session_id`       VARCHAR(255)
  COMMENT 'Session ID as determined by PHP',

  `type`             ENUM ('GET', 'POST', 'HEAD', 'PUT')
  COMMENT 'Request type',

  `url`              TEXT
  COMMENT 'URL of the request',

  PRIMARY KEY (`id`(128))
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

#####################################################
## Conversions table, replaces former "leads" table
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_conversions` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `page_id`          INT(11)          NOT NULL
  COMMENT 'FK to #__jinbound_pages table',

  `contact_id`       INT(11)          NOT NULL
  COMMENT 'FK to #__jinbound_contacts table',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'publication status of record - 0 is Unpublished, 1 is Published, -2 is Trashed',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time record was checked out',

  `formdata`         TEXT
  COMMENT 'JSON encoded form data',

  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Associates contacts to campaigns
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_campaigns` (

  `contact_id`  INT(11)    NOT NULL DEFAULT 0
  COMMENT 'FK to #__jinbound_contacts table',

  `campaign_id` INT(11)    NOT NULL DEFAULT 0
  COMMENT 'FK to #__jinbound_campaigns table',

  `enabled`     TINYINT(1) NOT NULL DEFAULT 1
  COMMENT 'enabled status, 1 = enabled, 0 = disabled',

  `added`       DATETIME   NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when contact was added to campaign, in UTC'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Tracks contact statuses over time
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_statuses` (

  `status_id`   INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_lead_statuses table',

  `campaign_id` INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_campaigns table',

  `contact_id`  INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_contacts table',

  `created`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`  INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`    DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by` INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## Tracks contact priorities over time
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_priorities` (

  `priority_id` INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_lead_statuses table',

  `campaign_id` INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_campaigns table',

  `contact_id`  INT(11)  NOT NULL
  COMMENT 'FK to #__jinbound_contacts table',

  `created`     DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC',

  `created_by`  INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of record creator',

  `modified`    DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was last modified in UTC',

  `modified_by` INT(11)  NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## reports email records
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_reports_emails` (

  `email`    TEXT     NOT NULL
  COMMENT 'Email the reports were sent to',

  `email_id` INT(11)  NOT NULL DEFAULT 0
  COMMENT 'References #__jinbound_emails.id',

  `created`  DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when record was created, in UTC'

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#####################################################
## pages hits records
#####################################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_landing_pages_hits` (

  `day`     DATE    NOT NULL,
  `page_id` INT(11) NOT NULL DEFAULT 0,
  `hits`    INT(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`day`, `page_id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

#############################################
##               Forms Table               ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_forms` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(10) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'FK to the #__assets table.',

  `title`            VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Form Title',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when form was created',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of form creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when form was last modified',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'Publication status',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'Date and Time form was checked out',

  `default`          TINYINT(1)       NOT NULL DEFAULT '0'
  COMMENT 'Determines if this is the default custom form',

  PRIMARY KEY (id)
)
  ENGINE = MyISAM;

#############################################
##               Fields Table              ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_fields` (

  `id`               INT(11)          NOT NULL AUTO_INCREMENT
  COMMENT 'Primary Key',

  `asset_id`         INT(10) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'FK to the #__assets table.',

  `name`             VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Field name attribute',

  `title`            VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Field Title (and label)',

  `type`             VARCHAR(255)     NOT NULL DEFAULT 'text'
  COMMENT 'Type of field, which must match a corresponding class/trigger',

  `description`      TEXT             NOT NULL DEFAULT ''
  COMMENT 'Description of field displayed to the end user',

  `default`          VARCHAR(255)     NOT NULL DEFAULT ''
  COMMENT 'Default value of the field',

  `params`           TEXT             NOT NULL DEFAULT ''
  COMMENT 'Various parameters for field - options, html attributes, etc',

  `created`          DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when field was created',

  `created_by`       INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of field creator',

  `modified`         DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when field was last modified',

  `modified_by`      INT(11)          NOT NULL DEFAULT '0'
  COMMENT 'User id of last modifier',

  `published`        TINYINT(1)                DEFAULT '0'
  COMMENT 'Publication status',

  `checked_out`      INT(11) UNSIGNED NOT NULL DEFAULT '0'
  COMMENT 'Locking column to prevent simultaneous updates',

  `checked_out_time` DATETIME         NOT NULL DEFAULT '0000-00-00 00:00:00'
  COMMENT 'when field was checked out',

  PRIMARY KEY (id)
)
  ENGINE = MyISAM;

#############################################
##         Form Fields Xref Table          ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_form_fields` (

  `form_id`  INT(11) NOT NULL
  COMMENT 'Primary key of form',

  `field_id` INT(11) NOT NULL
  COMMENT 'Primary key of field',

  `ordering` INT(11) NOT NULL DEFAULT 0
  COMMENT 'ordering of fields'

)
  ENGINE = MyISAM;

#############################################
##       Contact Follower Xref Table       ##
#############################################;

CREATE TABLE IF NOT EXISTS `#__jinbound_contacts_followers` (

  `contact_id`  INT(11) NOT NULL
  COMMENT 'Primary key of contact',

  `follower_id` INT(11) NOT NULL
  COMMENT 'Primary key of user',

  UNIQUE KEY `idx_contact_follower` (`contact_id`, `follower_id`)

)
  ENGINE = MyISAM;


