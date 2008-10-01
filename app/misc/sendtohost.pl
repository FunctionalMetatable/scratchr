#!/usr/local/bin/perl


$SWIFTCD_XML = <<XMLTEXT;
<?xml version="1.0" encoding="iso-8859-1"?>
<SwiftCDOrders CreateDt="12/1/2001 5:00pm" BatchId="100" SubmitterId="CustomerId" ContactInfo="no-spam\@no-spam.com">
     <Order>
          <CustomerId>AML1005</CustomerId> 
          <TransactionDate>9/5/2001 10:00am</TransactionDate> 
          <Items>
               <CD>
                    <TrackingId>20010307-860-1</TrackingId> 
                    <LabelId>1</LabelId> 
                    <PackageId>1</PackageId> 
                    <Quantity>1</Quantity> 
                    <RegKeyTitle>Licence number</RegKeyTitle> 
                    <RegKey>$LIC</RegKey> 
                    <RegFileName>Licence.txt</RegFileName> 
                    <RegFileContent>$LIC</RegFileContent> 
                   <Content>
                         <Product>
                              <ProductId>AML1005-001</ProductId> 
                              <Title>Shareware CD</Title> 
                         </Product>
                    </Content>
               </CD>
          </Items>
          <Shipping>
               <Method>1</Method> 
               <FirstName>Joe</FirstName> 
               <LastName>Consumer</LastName> 
               <Company>ABC Corporation</Company> 
               <Addr1>2295 Customer Way</Addr1> 
               <Addr2>Suite 100</Addr2> 
               <City>NoWhere</City> 
               <State>GA</State> 
               <Province></Province> 
               <Country>US</Country> 
               <Zip>30155</Zip> 
               <Email>email\@thenet.com</Email> 
               <Phone>(800) 555-6699</Phone> 
          </Shipping>
     </Order>
</SwiftCDOrders>
XMLTEXT


$SWIFTCD_XML =~ tr/\n//;

use LWP::UserAgent;
use HTTP::Request;

my $ua = LWP::UserAgent->new(env_proxy => 1, keep_alive => 1, timeout => 30, ); 

$response = 
$ua->request(HTTP::Request->new('POST', 'http://www.swiftcd.com/Orders/SubmitOrders.asp', 
HTTP::Headers->new(Content_Type => "text/xml"), $SWIFTCD_XML));

#-- Print the response
$reply = $response->as_string;

$reply =~ /<Success>(.)<\/Success.*<ConfirmationId>(.*)<\/Confirmation/;

print "$1\n";
print "$2\n";

exit;
