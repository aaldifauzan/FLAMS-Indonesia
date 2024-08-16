import json
import os

# Function to read and extract features from a GeoJSON file
def extract_features(file):
    with open(file) as f:
        data = json.load(f)
        if data['type'] == 'FeatureCollection':
            return data['features']
        elif data['type'] == 'Feature':
            return [data]
        else:
            raise ValueError(f"Unsupported GeoJSON type: {data['type']}")


# List all files in the directory
all_files = os.listdir()

# Filter files that start with '12.'
initial_files = [os.path.join(file) for file in all_files if file.startswith('96.')]

# Initialize an empty list to hold all features
all_features = []

# Extract and combine features from each filtered file
for file in initial_files:
    all_features.extend(extract_features(file))

# Create a new GeoJSON structure with the combined features
merged_geojson = {
    "type": "FeatureCollection",
    "features": all_features
}

# Save the merged GeoJSON to a new file
merged_file_path = os.path.join("96.geojson")
with open(merged_file_path, 'w') as f:
    json.dump(merged_geojson, f)

print(f"Merged GeoJSON saved to: {merged_file_path}")
