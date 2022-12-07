
# mw-WikiSearchLink
This is a mediawiki extension that crates an internal link to a WikiSearchLink page with some parameters

## What this does

This extension will change links to page such as that :

```[[Search|un lien vers Search "A un type de page=Exemple de mise en œuvre"]]```

will create a link of the form:

```<a href="/wiki/Search?filters=A+un+type+de+page%5E%5EExemple+de+mise+en+œuvre&order=desc&ordertype=Modification+date">un lien vers Search</a>```

This will only work for target page "Search".
