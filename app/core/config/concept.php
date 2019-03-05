<?php

index(globals)->App(autoloader)->Router(URI, params, [module, controller, action, params])
    ->Module([controller, action, params])->Controller(action, params)->View(params);