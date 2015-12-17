echo "
{
    \"method\" : \"sendMessage\",
    \"fromUserId\" : $1,
    \"toUserId\" : $2,
    \"body\" : \"$3\"
}" | ./reqsend.sh
