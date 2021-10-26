# ImageNameFormat

Project written to rename the photos I and the husband take of our little daughter Ellen.
I have an iPhone, husband a Pixel 5. 
I have to rename the photos with the original date of the file, husband has to remove the "PXL_" prefix of files.

## How to use for iPhone photos
I have installed the iCloud for Windows program, so I have the photos already available inside a folder already converted to .jpeg (while I take them in .heic).

    php .\minicli renamefromexif original=[path of the original files] destination=[path of the final files]
I usually copy the photos from iCloud to a local folder, and then apply the script to them, because I need a subset of all the photos I have. The destination folder can be the NAS one.

## How to use for Pixel photos
I usually run this script directly on the files in the NAS to remove the prefix.

    php .\minicli renameremovepxl folder=[path of the NAS folder]

## Old code: RenameFromOriginal
Before moving to Windows I was using Ubuntu and the Mac: the Mac to download the photos and convert them to jpeg, and finally the Ubuntu pc to store them.
The conversion operated by the Mac was removing all the exif data from the jpeg file, so I was using the heic files to gather the data and rename the jpeg files. I don't need this procedure no more, but I will keep it just in case.