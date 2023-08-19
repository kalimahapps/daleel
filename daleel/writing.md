# Writing Documentation
Daleel uses GitHub Flavored Markdown (GFM). It is rendered using [league/commonmark](https://commonmark.thephpleague.com/).

## Header Anchors
Daleel automatically generates header anchors for each header. You can link to a header by using the following syntax:
```markdown
[Code Blocks](#code-blocks)
```
This will render as:
[Code Blocks](#code-blocks)

You can also link to an anchor on another page by using the following syntax:
```markdown
[Top Level Configuration](/configuration#top-level-configuration)
```

This will render as:
[Top Level Configuration](/configuration#top-level-configuration)

## Links
When linking to an internal page, make sure it starts with a `/`. This will ensure that the link is relative to the root of the site. Also, omit adding an extension to the link.

For example:
```markdown
[Check Daleel Usage](/usage)
```

will render as:
[Check Daleel Usage](/usage)

External links will automatically have `target="_blank" rel="noopener noreferrer"` added to them. They will also have a small icon added to the end of the link.

## Table of Contents
Daleel automatically generates a table of contents for each page. You can see it in action on this page to the right if you are on a desktop.

## Page Title
The page title is automatically generated from the first header on the page. You can add a custom page title by using frontmatter:
```yml
---
title: Custom Page Title
---
```

## Table
You can create a table using the following syntax:
```markdown
| #   | Country   |     Capital      |  Population |
| --- | --------- | :--------------: | ----------: |
| 1   | USA       | Washington, D.C. | 328,239,523 |
| 2   | Canada    |      Ottawa      |  37,411,047 |
| 3   | Mexico    |   Mexico City    | 126,014,024 |
| 4   | Brazil    |     Brasilia     | 209,469,333 |
| 5   | Argentina |   Buenos Aires   |  44,938,712 |
| 6   | Colombia  |      Bogota      |  50,372,424 |
| 7   | Peru      |       Lima       |  32,510,453 |
```
This will render as:
| #   | Country   |     Capital      |  Population |
| --- | --------- | :--------------: | ----------: |
| 1   | USA       | Washington, D.C. | 328,239,523 |
| 2   | Canada    |      Ottawa      |  37,411,047 |
| 3   | Mexico    |   Mexico City    | 126,014,024 |
| 4   | Brazil    |     Brasilia     | 209,469,333 |
| 5   | Argentina |   Buenos Aires   |  44,938,712 |
| 6   | Colombia  |      Bogota      |  50,372,424 |
| 7   | Peru      |       Lima       |  32,510,453 |


Overflow tables will automatically scroll horizontally:
| #   | Country   |     Capital      |  Population | ISO Code |   Language | Currency | Size (km²) | Status | Continent | Region | Subregion | Calling Code |
| --- | --------- | :--------------: | ----------: | -------: | ---------: | -------: | ---------: | -----: | --------: | -----: | --------: | -----------: |
| 1   | USA       | Washington, D.C. | 328,239,523 |       US |    English |      USD |  9,833,517 |      1 |     North |     19 |        21 |            1 |
| 2   | Canada    |      Ottawa      |  37,411,047 |       CA |    English |      CAD |  9,984,670 |      1 |     North |     19 |        21 |            1 |
| 3   | Mexico    |   Mexico City    | 126,014,024 |       MX |    Spanish |      MXN |  1,964,375 |      1 |     North |     19 |        21 |           52 |
| 4   | Brazil    |     Brasilia     | 209,469,333 |       BR | Portuguese |      BRL |  8,515,767 |      1 |     North |     19 |        21 |           55 |
| 5   | Argentina |   Buenos Aires   |  44,938,712 |       AR |    Spanish |      ARS |  2,780,400 |      1 |     North |     19 |        21 |           54 |
| 6   | Colombia  |      Bogota      |  50,372,424 |       CO |    Spanish |      COP |  1,141,748 |      1 |     North |     19 |        21 |           57 |
| 7   | Peru      |       Lima       |  32,510,453 |       PE |    Spanish |      PEN |  1,285,216 |      1 |     North |     19 |        21 |           51 |


## Code Blocks
Daleel uses [commonmark-highlighter](https://github.com/spatie/commonmark-highlighter) to render code blocks.

#### input
````markdown
```php
echo 'Hello World!';
```
````

````markdown
```html
<ul>
	<li> Item 1 </li>
	<li> Item 2 </li>
	<li> Item 3 </li>
</ul>
```
````

#### output
```php
echo 'Hello World!';
```

```html
<ul>
	<li> Item 1 </li>
	<li> Item 2 </li>
	<li> Item 3 </li>
</ul>
```

## Line Highlighting in Code Blocks
You can highlight a single line in a code block by adding curly braces with the line number `{4}`
#### input
````markdown
```php{4}
<?php
function helloWorld()
{
	echo 'Hello World!';
}
```
````

#### output
```php{4}
<?php
function helloWorld()
{
	echo 'Hello World!';
}
```

You can also highlight a range of lines by using a hyphen `{3-5}`
#### input
````markdown
```php{2-4}
<?php
$fruit = [
	'apple',
	'banana',
	'orange',
	'pear',
	'grape',
	'kiwi',
	'pineapple',
];
```
````

#### output
```php{3-5}
<?php
$fruit = [
	'apple',
	'banana',
	'orange',
	'pear',
	'grape',
	'kiwi',
	'pineapple',
];
```

Or you can combine the two `{3-5,8}`:
#### input
````markdown
```php{3-5,8}
<?php
$fruit = [
	'apple',
	'banana',
	'orange',
	'pear',
	'grape',
	'kiwi',
	'pineapple',
];
```
````

#### output
```php{3-5,8}
<?php
$fruit = [
	'apple',
	'banana',
	'orange',
	'pear',
	'grape',
	'kiwi',
	'pineapple',
];
```

## Containers
Daleel comes with a few custom containers that you can utilize to make your documentation more readable.

#### input
```markdown
:::info
This is an info box.
:::

:::tip
This is a tip.
:::

:::warning
This is a warning.
:::

:::danger
This is a dangerous warning.
:::
```

#### output
:::info
This is an info box.
:::

:::tip
This is a tip.
:::

:::warning
This is a warning.
:::

:::danger
This is a dangerous warning.
:::

You can add a custom title to the container by adding it after the container name:
```markdown
:::info Custom Title
This is an info box with a custom title.
:::
```

:::info Custom Title
This is an info box with a custom title.
:::