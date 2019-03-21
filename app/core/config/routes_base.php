<?php

return [

    '' => RouterOld::DEFAULT_MODULE . '/' . RouterOld::DEFAULT_CONTROLLER . '/' . RouterOld::DEFAULT_ACTION,

    RouterOld::DEFAULT_ACTION => RouterOld::DEFAULT_MODULE . '/' . RouterOld::DEFAULT_CONTROLLER . '/' . RouterOld::DEFAULT_ACTION,
    RouterOld::DEFAULT_CONTROLLER => RouterOld::DEFAULT_MODULE . '/' . RouterOld::DEFAULT_CONTROLLER . '/' . RouterOld::DEFAULT_ACTION,
    RouterOld::DEFAULT_MODULE => RouterOld::DEFAULT_MODULE . '/' . RouterOld::DEFAULT_CONTROLLER . '/' . RouterOld::DEFAULT_ACTION,

    '<module:[a-z]+>' => '<module>/' . RouterOld::DEFAULT_CONTROLLER . '/' . RouterOld::DEFAULT_ACTION,
    '<controller:[a-z]+>' => RouterOld::DEFAULT_MODULE . '/<controller>/' . RouterOld::DEFAULT_ACTION,
    '<action:[a-z]+>' => RouterOld::DEFAULT_MODULE . '/' . RouterOld::DEFAULT_CONTROLLER . '/<action>',

    '<module:[a-z]+>/<controller:[a-z]+>' => '<module>/<controller>/' . RouterOld::DEFAULT_ACTION,
    '<controller:[a-z]+>/<action:[a-z]+>' => RouterOld::DEFAULT_MODULE . '/<controller>/<action>',
    '<module:[a-z]+>/<action:[a-z]+>' => '<module>/' . RouterOld::DEFAULT_CONTROLLER . '/<action>',

    '<module:[a-z]+>/<controller:[a-z]+>/<action:[a-z]+>' => '<module>/<controller>/<action>',

];