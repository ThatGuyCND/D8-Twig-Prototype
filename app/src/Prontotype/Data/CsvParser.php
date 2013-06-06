<?php

namespace Prontotype\Data;

Class CsvParser extends Parser {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getHandledExtensions()
    {
        return array(
            'csv'
        );
    }
    
    public function parse($content)
    {
        if ( ! is_string($content) ) {
            throw new \Exception('CSV data format error');
        }
        $config = $this->app['config']['data']['csv'];
        $data = $this->csvToArray($content, $config['escape'], $config['enclosure'],  $config['delimiter']);
        if ( $data && $config['headers'] ) {
            $indexedData = array();
            $headerData = $data[0];
            unset($data[0]);
            $data = array_values($data);
            foreach( $data as $row ) {
                $rowData = array();
                for($i = 0; $i < count($row); $i++) {
                    if ( isset($headerData[$i])) {
                        $rowData[$headerData[$i]] = $row[$i];
                    } else {
                        $rowData[$i] = $row[$i];
                    }
                }
                if ( $config['id_header'] && isset($rowData[$config['id_header']]) ) {
                    $indexedData[$rowData[$config['id_header']]] = $rowData;
                } else {
                    $indexedData[] = $rowData;
                }
            }
            $data = $indexedData;
        }
        return $data;
    }
    
    function csvToArray($fileContent,$escape = '\\', $enclosure = '"', $delimiter = '\\n')
    {
        $lines = array();
        $fields = array();

        if ($escape == $enclosure) {
            $escape = '\\';
            $fileContent = str_replace(array('\\',$enclosure.$enclosure,"\r\n","\r"),
            array('\\\\',$escape.$enclosure,"\\n","\\n"),$fileContent);
        } else {
            $fileContent = str_replace(array("\r\n","\r"),array("\\n","\\n"),$fileContent);
        }

        $nb = strlen($fileContent);
        $field = '';
        $inEnclosure = false;
        $previous = '';

        for ($i = 0;$i<$nb; $i++) {
            $c = $fileContent[$i];
            if ($c === $enclosure) {
                if ($previous !== $escape) {
                    $inEnclosure ^= true;
                } else {
                    $field .= $enclosure;
                }
            } elseif ($c === $escape) {
                $next = $fileContent[$i+1];
                if ($next != $enclosure && $next != $escape) {
                    $field .= $escape;
                } 
            } elseif ($c === $delimiter) {
                if ($inEnclosure) {
                    $field .= $delimiter;
                } else {
                    $fields[] = $field;
                    $field = '';
                }
            } elseif ($c === "\n") {
                $fields[] = $field;
                $field = '';
                $lines[] = $fields;
                $fields = array();
            } else {
                $field .= $c;
            }   
            $previous = $c;
        }
        if ($field !== '') {
            $fields[] = $field;
            $lines[] = $fields;
        }
        return $lines;
    }
    
    // protected function parse_csv( $path )
 //    {
 //        $config = $this->app['config']['data']['csv'];
 //        try {
 //            $row = 1;
 //            $data_array = array();
 //            $headers = array();
 //            $id_col = FALSE;
 //            
 //            if ( strpos($path,'http') === 0 ) {
 //                // external url
 //                $handle = fopen('php://temp', 'w+');
 //                $curl = curl_init();
 //                curl_setopt($curl, CURLOPT_URL, $path);
 //                curl_setopt($curl, CURLOPT_FILE, $handle);
 //                curl_exec($curl);
 //                curl_close($curl);
 //                rewind($handle);
 //            } else {
 //                // local file
 //                $handle = fopen($path, "r");
 //            }
 //            
 //            if ( $handle !== FALSE ) {
 //                while (($data = fgetcsv($handle, 0, $config['delimiter'], $config['enclosure'], $config['escape'] )) !== FALSE) {
 //                    $has_headers = $config['headers'];
 //                    if ( $row == 1 && $has_headers ) {
 //                        $headers = $data; // set headers
 //                        $id_col = array_search($config['id_header'], $headers );                        
 //                    } elseif ( $has_headers ) {
 //                        $row_data = array();
 //                        for ( $i = 0; $i < count( $data ); $i++ ) {
 //                            $row_data[$headers[$i]] = $data[$i];
 //                        }
 //                        if ( $id_col !== FALSE ) {
 //                            $data_array[$data[$id_col]] = $row_data;
 //                        } else {
 //                            $data_array[] = $row_data;
 //                        }
 //                    } else {
 //                        $data_array[] = $data;
 //                    }
 //                    $row++;
 //                }
 //                fclose($handle);
 //            }
 //            return $data_array;
 //        } catch( \Exception $e ) {           
 //            throw new \Exception('CSV data format error in ' . $path);
 //        }
 //    }
    
}
