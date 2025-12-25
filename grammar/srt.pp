/**
 * SRT Subtitle File Grammar
 */

// Skip BOM 
%skip   T_BOM           \x{FEFF}

// Tokens in order of specificity
%token  T_TIMECODE      \d{2}:\d{2}:\d{2},\d{3}
%token  T_ARROW         \h*-->\h*
%token  T_NUMBER        (?<=^)\d+(?=\r?\n)
%token  T_BLANK         \r?\n\r?\n
%token  T_NEWLINE       \r?\n
%token  T_TEXT          (?-s).+

#Document -> Korobochkin\SrtReader\Ast\SrtDocumentNode
    : Block()*
    ;

#Block -> Korobochkin\SrtReader\Ast\SrtBlockNode
    : <T_NUMBER> ::T_NEWLINE::
      Timecode() ::T_NEWLINE::
      TextLines()
      ::T_BLANK::?
    ;

#Timecode
    : <T_TIMECODE> ::T_ARROW:: <T_TIMECODE>
    ;

#TextLines
    : TextLine()+
    ;

TextLine
    : <T_TEXT> ::T_NEWLINE::?
    ;
