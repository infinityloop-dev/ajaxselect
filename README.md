# Ajaxselect
:wrench: Ajax-filled selectbox for nette forms.

![Packagist](https://img.shields.io/packagist/dt/nepttune/ajaxselect.svg)
![Packagist](https://img.shields.io/packagist/v/nepttune/ajaxselect.svg)
[![CommitsSinceTag](https://img.shields.io/github/commits-since/nepttune/ajaxselect/v1.0.svg?maxAge=600)]()

[![Code Climate](https://codeclimate.com/github/nepttune/ajaxselect/badges/gpa.svg)](https://codeclimate.com/github/nepttune/ajaxselect)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nepttune/ajaxselect/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/nepttune/ajaxselect/?branch=master)

## Introduction

This extension provides easy to use ajax-driven selectbox.

## Dependencies

- [nepttune/base-requirements](https://github.com/nepttune/base-requirements)

## How to use

- Register `\Nepttune\DI\AjaxSelectExtension` as nette extension.
- Use `addAjaxSelect` or `addAjaxMultiSelect` in your forms.

### Example configuration

```
extensions:
    ajaxSelect: Nepttune\DI\AjaxSelectExtension
```

### Example form

```
$form->addAjaxSelect('client_id', 'Klient', function (string $query, ?int $default = 0) {
      if ($default) {
          $row = $this->repository->getRow($default);
          return [$row->id => $row->name];
      }

      return $this->repository->search($query);
  })
  ->setPrompt('--- Vyberte ---')
  ->setRequired();
```

Parameter `$query` contains text being searched, parametered `$default` contains value which is set as default (for example when  editing existing entry, you need to provide saved key => value in your callback).

### Javascript snippet using select2
```
if ($(this).data('ajaxselect')) {
    $(this).select2({
        tokenSeparators: [',', ' '],
        ajax: {
            url: $(this).data('ajaxselect'),
            delay: 250,
            dataType: 'json',
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data, params) {
                var result = [];
                $.each(data, function (key, value) {
                    result.push({
                        id: key,
                        text: value
                    });
                });
                return {
                    results: result
                };
            }
        }
    });
}
```
