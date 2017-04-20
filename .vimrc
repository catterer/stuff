if v:lang =~ "utf8$" || v:lang =~ "UTF-8$"
   set fileencodings=ucs-bom,utf-8,latin1
endif

set exrc
set nocompatible	" Use Vim defaults (much better!)
set bs=indent,eol,start		" allow backspacing over everything in insert mode
"set ai			" always set autoindenting on
"set backup		" keep a backup file
set viminfo='20,\"50	" read/write a .viminfo file, don't store more
			" than 50 lines of registers
set history=50		" keep 50 lines of command line history
set ruler		" show the cursor position all the time

" Only do this part when compiled with support for autocommands
if has("autocmd")
  augroup fedora
  autocmd!
  " In text files, always limit the width of text to 78 characters
  " autocmd BufRead *.txt set tw=78
  " When editing a file, always jump to the last cursor position
  autocmd BufReadPost *
  \ if line("'\"") > 0 && line ("'\"") <= line("$") |
  \   exe "normal! g'\"" |
  \ endif
  " don't write swapfile on most commonly used directories for NFS mounts or USB sticks
  autocmd BufNewFile,BufReadPre /media/*,/mnt/* set directory=~/tmp,/var/tmp,/tmp
  " start with spec file template
  autocmd BufNewFile *.spec 0r /usr/share/vim/vimfiles/template.spec
  augroup END
endif

if has("cscope") && filereadable("/usr/bin/cscope")
   set csprg=/usr/bin/cscope
   set csto=0
   set cst
   set nocsverb
   " add any database in current directory
   if filereadable("cscope.out")
      cs add cscope.out
   " else add database pointed to by environment
   elseif $CSCOPE_DB != ""
      cs add $CSCOPE_DB
   endif
   set csverb
endif

" Switch syntax highlighting on, when the terminal has colors
" Also switch on highlighting the last used search pattern.
if &t_Co > 2 || has("gui_running")
  syntax on
  set hlsearch
endif

filetype plugin on

if &term=="xterm"
     set t_Co=8
     set t_Sb=[4%dm
     set t_Sf=[3%dm
endif

" Don't wake up system with blinking cursor:
" http://www.linuxpowertop.org/known.php
let &guicursor = &guicursor . ",a:blinkon0"

set autoindent
set nonumber
set smartindent
set cindent
set nobackup


" Encoding menu. Press F8 to change.
set wildmenu
set wcm=<Tab>
menu Encoding.koi8-r :e ++enc=koi8-r ++ff=unix<CR>
menu Encoding.windows-1251 :e ++enc=cp1251 ++ff=dos<CR>
menu Encoding.cp866 :e ++enc=cp866 ++ff=dos<CR>
menu Encoding.utf-8 :e ++enc=utf8 <CR>
menu Encoding.koi8-u :e ++enc=koi8-u ++ff=unix<CR>
map <F8> :emenu Encoding.<TAB>

map <F7> :set softtabstop=8<CR>:set shiftwidth=8<CR>
" map <F8> :set softtabstop=4<CR>:set shiftwidth=4<CR>
" map <F6> :vimgrep /fixme\\|todo/j *.[c,h]<CR>:cw<CR>

" C comment line
map 1q i<Home>//<ESC>
map! 1q <Home>//
" C uncomment line
map 2e :s/\/\///<CR>
map! 2e <ESC>:s/\/\///<CR>i
" Perl comment line
map `q i<Home>#<ESC>
map! `q <Home>#

map <S-Insert> <MiddleMouse>
map! <S-Insert> <MiddleMouse>

" Next file
map <F3> :n<CR>
map! <F3> <ESC>:n<CR>i
" Prev file
map <F6> :colorscheme peachpuff<CR>
map! <F6> <ESC>:colorscheme peachpuff<CR>i

" Prev file
map <F2> :prev<CR>
map! <F2> <ESC>:prev<CR>i

set nohlsearch

" Ctrl+\ - open definition in new tab
map <C-\> :tab split<CR>:exec("tag ".expand("<cword>"))<CR>

" Alt + ] - open definition in vertical split
map <C-s> :vsp <CR>:exec("tag ".expand("<cword>"))<CR>

set winheight=30
set winminheight=5
" Toggle through splits
map <Tab> <C-w>w :exe "resize " . (winheight(0) * 3/2)<CR>

" Closing file
map qq :q!<CR>

" Closing all files
map <S-q> :qall<CR>

" Opening files
map <C-o> :tabe .<CR>

" Searching for tags in parent directory
" autocmd BufRead *.c,*.h set tags=tags

" Toggle hlsearch
:noremap <F4> :set hlsearch! hlsearch?<CR>

" Something else
set background=dark
set tf
set showcmd
set incsearch
set tabstop=8
set expandtab
set softtabstop=4
set shiftwidth=4
set shiftround
set path+=**

" Normal copy paste
:noremap <F5> :set paste! paste?<CR>

nmap <F9> :TagbarToggle<CR>

function BuildImagine()
    let g:App = fnamemodify(system('find . -name app.asm'), ':h:t')
    execute "set makeprg=./asm\\ ".g:App
endfunction
"call BuildImagine()

" make
map <F12> :make<CR>
map! <F12> <ESC>:make<CR>i

" Find visually highlighted text
vmap // y/<C-R>"<CR>
map \\ :vimgrep "<C-R>/" **/*c **/*h<CR>:cw<CR>

map <C-\> :!ctags -R *<CR>
map! <C-\> <ESC>:!ctags -R *<CR>i
"source ~/.vim/colors/wombat256mod.vim
"source $VIMRUNTIME/colors/wombat256mod.vim
set lazyredraw
set cursorline
set so=5
set noswapfile
colorscheme slate
set timeoutlen=300
set nofsync

execute pathogen#infect()
"colorscheme wombat256mod
colorscheme peachpuff
autocmd BufNewFile,BufRead *.go   set syntax=go
autocmd BufNewFile,BufRead *.cc   set syntax=cpp11
autocmd BufNewFile,BufRead *.hh   set syntax=cpp11
autocmd BufNewFile,BufRead *.h    set syntax=cpp11
autocmd BufNewFile,BufRead *.hpp  set syntax=cpp11
autocmd BufNewFile,BufRead *.cpp  set syntax=cpp11

nnoremap <silent> gl "_yiw:s/\(\%#\w\+\)\(\_W\+\)\(\w\+\)/\3\2\1/<CR><c-o>/\w\+\_W\+<CR><c-l>

" If you want :UltiSnipsEdit to split your window.
let g:UltiSnipsEditSplit="vertical"
let g:UltiSnipsSnippetDirectories = ['/home/a.simonov/.vim/bundle/ultisnips']

let g:UltiSnipsExpandTrigger='<c-e>'            " tried <c-a> and failed too
let g:UltiSnipsListSnippets='<c-q>'            " tried <c-a> and failed too
let g:UltiSnipsJumpForwardTrigger='<c-j>'  " tried <c-j> and failed too
let g:UltiSnipsJumpBackwardTrigger='<c-b>'

set cino=>:0,g0,N-s
