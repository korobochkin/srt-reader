# SRT reader

This project uses the [phplrt](https://github.com/phplrt/phplrt) library to parse `.srt` files. While phplrt is a very interesting and powerful project, it suffers from imperfect documentation and a lack of examples. Below you can find some information about how both this code and phplrt work.

## Usage

See [`development/examples/base-example.php`](development/examples/base-example.php) for an example.

## Terms

| Term     | What It Is                                                  |
|----------|-------------------------------------------------------------|
| Lexer    | Breaks text into tokens (`T_INDEX`, `T_TEXT`, etc.)         |
| Parser   | Takes tokens, applies grammar rules, builds tree            |
| Grammar  | Rules that describe valid structure (`Block : <T_INDEX>`)   |
| Compiler | Reads `.pp` grammar file, produces lexer + parser config    |
| AST      | Abstract Syntax Tree — the parsed result as objects         |
| Builder  | Creates AST nodes from matched rules                        |
| Reducers | PHP code that transforms matched tokens into custom objects |

## Processing steps

```text
Input: "1\n00:00:01,111 --> 00:00:02,222\nHello world\n\n"
        │
        ▼
┌────────────────────────────────────────────────────────────────┐
│  LEXER                                                         │
│  Breaks into tokens using regex patterns                       │
│                                                                │
│  %token  T_TIMECODE      (\d{2})[,.:，．。：](\d{2})[,.:，．。   │
│  %token  T_ARROW         \h*-->\h*                             │
│  %token  T_INDEX         (?<=^)\d+(?=\r?\n)                    │
└────────────────────────────────────────────────────────────────┘
        │
        ▼
  [T_INDEX:"1"] [T_NEWLINE] [T_TIMECODE:"00:00:01,111"] 
  [T_ARROW] [T_TIMECODE:"00:00:02,222"] [T_NEWLINE] 
  [T_TEXT:"Hello world"] [T_BLANK]
        │
        ▼
┌────────────────────────────────────────────────────────────────┐
│  PARSER                                                        │
│  Matches token sequences against rules                         │
│                                                                │
│  #Block -> { return SrtBlockNodeFactory::create($children); }  │
│   : <T_INDEX> ::T_NEWLINE::                                    │
│     Timecode() ::T_NEWLINE::                                   │
│     TextLines()                                                │
│     ::T_BLANK::?                                               │
│   ;                                                            │
└────────────────────────────────────────────────────────────────┘
        │
        ▼
┌────────────────────────────────────────────────────────────────┐
│  AST (Abstract Syntax Tree)                                    │
│                                                                │
│  Document {SrtDocumentNode}                                    │
│  └── children {array[N]}                                       │
│      ├── 0 Block {SrtBlockNode}                                │
│      │   ├── index 1 {int} // T_INDEX                          │
│      │   ├── startTime 1111 {int} // T_TIMECODE                │
│      │   ├── endTime 2222 {int} // T_TIMECODE                  │
│      │   └── text Hello world {string} // T_TEXT               │
│      └── 1 Block ...                                           │
└────────────────────────────────────────────────────────────────┘
```
