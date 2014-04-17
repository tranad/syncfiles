# [INSTALL]

## get Wombat color scheme
> mkdir -p ~/.vim/colors && cd ~/.vim/colors && wget -O wombat256mod.vim http://www.vim.org/scripts/download_script.php?src_id=13400

## soft link rc files to git files
> rm ~/.bashrc && ln -s ~/syncfiles/dotfiles/bashrc ~/.bashrc
> rm ~/.screenrc && ln -s ~/syncfiles/dotfiles/screenrc ~/.screenrc
> rm ~/.vimrc && ln -s ~/syncfiles/dotfiles/vimrc ~/.vimrc

## pathogen (https://github.com/tpope/vim-pathogen)
> mkdir -p ~/.vim/autoload ~/.vim/bundle
> curl -Sso ~/.vim/autoload/pathogen.vim https://raw.github.com/tpope/vim-pathogen/master/autoload/pathogen.vim

## NERDtree (https://github.com/scrooloose/nerdtree)
> cd ~/.vim/bundle
> git clone https://github.com/scrooloose/nerdtree.git

## syntastic (https://github.com/scrooloose/syntastic)
> cd ~/.vim/bundle
> git clone https://github.com/scrooloose/syntastic.git

## Toggle plugin
> mkdir -p ~/.vim/plugin
> wget http://www.vim.org/scripts/download_script.php?src_id=13834 -O toggle.vim
> cp toggle.vim ~/.vim/plugin && rm toggle.vim

# [COPY&PASTE]
``` bash
mkdir -p ~/.vim/colors && cd ~/.vim/colors && wget -O wombat256mod.vim http://www.vim.org/scripts/download_script.php?src_id=13400
xrdb -merge ~/.Xresources
mkdir -p ~/.vim/autoload ~/.vim/bundle
curl -Sso ~/.vim/autoload/pathogen.vim https://raw.github.com/tpope/vim-pathogen/master/autoload/pathogen.vim
cd ~/.vim/bundle
git clone https://github.com/scrooloose/nerdtree.git
git clone https://github.com/scrooloose/syntastic.git
mkdir -p ~/.vim/plugin
wget http://www.vim.org/scripts/download_script.php?src_id=13834 -O toggle.vim
cp toggle.vim ~/.vim/plugin && rm toggle.vim

rm ~/.bashrc && ln -s ~/syncfiles/dotfiles/bashrc ~/.bashrc
rm ~/.screenrc && ln -s ~/syncfiles/dotfiles/screenrc ~/.screenrc
rm ~/.vimrc && ln -s ~/syncfiles/dotfiles/vimrc ~/.vimrc
rm ~/.Xresources && ln -s ~/syncfiles/dotfiles/Xresources ~/.Xresources
```

# [PYFILES]
## miscutils.py
Contains various functions that would find common use in python. My hack for allowing importing of miscutils would be:
``` python
import sys
sys.path.append('~/syncfiles/pyfiles')
```

## stats.py
Takes piped input and prints out length, mean, sigma, sum, min, max. It can ignore non-numerical lines, but it only handles 1 column. If specified, the first argument of stats.py provides the column of piped input to use
``` bash
seq 1 1 10 | stats
```
produces
```
        length: 10
        mean:   5.5
        sigma:  3.0276503541
        sum:    55.0
        min:    1.0
        max:    10.0
```

## histo.py
Uses the dumb terminal setting in gnuplot to display a text histogram of the piped data. Currently does not allow column specification, so that must be provided before piping. This requires a single argument of the binwidth

``` bash
# sampling a uniform random number 4 times and summing gives something 
# close to a gaussian by the central limit theorem!
for i in {1..10000}; do echo $(( (RANDOM+RANDOM+RANDOM+RANDOM)/4 )); done | histo 2000
```
produces
```
  1800 ++---------+-----------+----------+----------+-----------+---------++
       +          +           +  "-" using (bin($1,binwidth)):(1.0) ****** +
  1600 ++                                ******                           ++
       |                            ******    *                            |
  1400 ++                           *    *    *****                       ++
       |                        *****    *    *   *                        |
  1200 ++                       *   *    *    *   *                       ++
       |                        *   *    *    *   *                        |
  1000 ++                       *   *    *    *   ******                  ++
       |                        *   *    *    *   *    *                   |
       |                   ******   *    *    *   *    *                   |
   800 ++                  *    *   *    *    *   *    *                  ++
       |                   *    *   *    *    *   *    *****               |
   600 ++              *****    *   *    *    *   *    *   *              ++
       |               *   *    *   *    *    *   *    *   *               |
   400 ++              *   *    *   *    *    *   *    *   ******         ++
       |          ******   *    *   *    *    *   *    *   *    *          |
   200 ++         *    *   *    *   *    *    *   *    *   *    *         ++
       +      *****    *   *  + *   *    *    *   * +  *   *    *****      +
     0 ++*******************************************************************
       0         5000       10000      15000      20000       25000      30000
```
