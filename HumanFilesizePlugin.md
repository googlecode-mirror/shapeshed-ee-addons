# Introduction #

This file must be placed in the
/system/plugins/ folder in your ExpressionEngine installation.


# Details #

This plugin returns the size of a file in human readable format (e.g 101.34 KB, 10.41 GB )

Wrap the absolute path filename in these tags to have it processed

```
{exp:ss_human_filesize}/uploads/documents/your_document.pdf{/exp:ss_human_filesize}
```

If you are using Mark Huot's File extension you can just use the EE tag you chose for the file field

```
{exp:ss_human_file_size}{your_file_field}{/exp:ss_human_file_size}
```

The function calculates whether to show KB, MB or GB depending on the file size.