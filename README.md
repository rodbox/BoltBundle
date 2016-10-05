# RBBoltBundle :

Bolt pour RB en version Alphas. Constructeur de chemin d'action dans un input depuis differentes sources.

## routing :
```
rb_bolt:
    resource: "@RBBoltBundle/Controller/"
    type:     annotation
    prefix:   /
```

## config :
```
    - { resource: "@RBBoltBundle/Resources/config/services.yml" }
```

## AppKernel.php :
```
 public function registerBundles()
    {
        $bundles = [
           ...
            new RB\BoltBundle\RBBoltBundle(),
```

# Important : créer les fichiers :
- /app/config/rb_config.yml
- /app/config/rb_config.yml.dist
Ils doivent contenir les liens absolue et les urls vers les différents dossier et fichiers de l'application RB.
